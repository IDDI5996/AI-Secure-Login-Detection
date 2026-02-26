<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AiLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Security\SecurityActionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::post('/login', [AiLoginController::class, 'login']);
Route::post('/verify-login', [AiLoginController::class, 'verifyLogin']);
Route::post('/request-verification/{method}', [VerificationController::class, 'requestVerification']);

// Authenticated routes (require Sanctum token)
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'role' => $request->user()->role
        ]);
    });
    
    // User search endpoint
    Route::get('/users/search', function (Request $request) {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $users = \App\Models\User::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->where('id', '!=', auth()->id()) // Don't show current user
            ->limit(10)
            ->get(['id', 'name', 'email', 'is_locked', 'role'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_locked' => (bool)$user->is_locked,
                    'role' => $user->role
                ];
            });
        
        return response()->json($users);
    });
    
    // User management
    Route::get('/user/login-history', [AuditLogController::class, 'getUserLoginHistory']);
    Route::post('/user/update-security-questions', [UserController::class, 'updateSecurityQuestions']);
    Route::post('/user/enable-2fa', [UserController::class, 'enableTwoFactor']);
    
    // Logout
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });
    
    // Security API Routes
    Route::prefix('security')->group(function () {
        Route::post('/lock-system', [SecurityActionsController::class, 'lockSystem']);
        Route::post('/unlock-system', [SecurityActionsController::class, 'unlockSystem']);
        Route::post('/block-ip', [SecurityActionsController::class, 'blockIpAddress']);
        Route::post('/unblock-ip/{id}', [SecurityActionsController::class, 'unblockIpAddress']);
        Route::post('/require-2fa', [SecurityActionsController::class, 'require2FA']);
        Route::post('/lock-user', [SecurityActionsController::class, 'lockUserAccount']);
        Route::post('/unlock-user/{id}', [SecurityActionsController::class, 'unlockUserAccount']);
        Route::post('/generate-report', [SecurityActionsController::class, 'generateReport']);
        Route::post('/update-threat-db', [SecurityActionsController::class, 'updateThreatDatabase']);
        
        // Get endpoints
        Route::get('/blocked-ips', [SecurityActionsController::class, 'getBlockedIps']);
        Route::get('/locked-users', [SecurityActionsController::class, 'getLockedUsers']);
        Route::get('/system-lock-status', [SecurityActionsController::class, 'getSystemLockStatus']);
    });
    
    // Admin routes (additional admin middleware)
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard']);
        Route::get('/suspicious-activities', [DashboardController::class, 'suspiciousActivities']);
        Route::get('/suspicious-activities/{id}', [DashboardController::class, 'getSuspiciousActivity']);
        Route::put('/suspicious-activities/{id}', [DashboardController::class, 'updateActivityStatus']);
        Route::get('/users/{id}/login-history', [DashboardController::class, 'userLoginHistory']);
        Route::get('/risk-report', [DashboardController::class, 'getRiskReport']);
        Route::get('/real-time-stats', [DashboardController::class, 'getRealTimeStats']);
        
        // Export data
        Route::get('/export/login-attempts', [ExportController::class, 'exportLoginAttempts']);
        Route::get('/export/suspicious-activities', [ExportController::class, 'exportSuspiciousActivities']);
    });
});