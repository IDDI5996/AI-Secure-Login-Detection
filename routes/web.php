<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AiLoginController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Security\SecurityActionsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\DashboardController as MainDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Public authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AiLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AiLoginController::class, 'login']);
    Route::get('/verify', [AiLoginController::class, 'showVerification'])->name('verify');
    Route::post('/verify/2fa', [AiLoginController::class, 'verify2FA'])->name('verify.2fa');
    Route::post('/verify/email', [AiLoginController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/verify/questions', [AiLoginController::class, 'verifyQuestions'])->name('verify.questions');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [MainDashboardController::class, 'index'])->name('dashboard');
    
    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log');
        Route::get('/audit-log/{id}', [AuditLogController::class, 'show'])->name('login-attempt-detail');
        Route::get('/suspicious-activities', [AdminDashboardController::class, 'suspiciousActivities'])->name('suspicious-activities');
    
        // JSON endpoint for modal
        Route::get('/suspicious-activity/{id}/details', [AdminDashboardController::class, 'getActivityDetails'])->name('activity-details');
    
        Route::put('/suspicious-activities/{id}', [AdminDashboardController::class, 'updateActivity'])->name('update-activity');
        Route::get('/risk-report', [AdminDashboardController::class, 'riskReport'])->name('risk-report');
        Route::get('/export-audit-log', [AuditLogController::class, 'export'])->name('export-audit-log');
    });
    
    // Security-specific routes
    Route::middleware(['security'])->prefix('security')->name('security.')->group(function () {
        Route::get('/dashboard', function () {
            return view('security.dashboard');
        })->name('dashboard');
        
        Route::get('/reviews', function () {
            return view('security.reviews');
        })->name('reviews');
        
        Route::get('/alerts', function () {
            return view('security.alerts');
        })->name('alerts');
        
        Route::get('/risk-analysis', function () {
            return view('security.risk-analysis');
        })->name('risk-analysis');
        
        // Security actions (AJAX/API routes)
        Route::prefix('actions')->name('actions.')->group(function () {
            Route::post('/lock-system', [SecurityActionsController::class, 'lockSystem'])->name('lock-system');
            Route::post('/unlock-system', [SecurityActionsController::class, 'unlockSystem'])->name('unlock-system');
            Route::post('/block-ip', [SecurityActionsController::class, 'blockIpAddress'])->name('block-ip');
            Route::post('/unblock-ip/{id}', [SecurityActionsController::class, 'unblockIpAddress'])->name('unblock-ip');
            Route::post('/require-2fa', [SecurityActionsController::class, 'require2FA'])->name('require-2fa');
            Route::post('/lock-user', [SecurityActionsController::class, 'lockUserAccount'])->name('lock-user');
            Route::post('/unlock-user/{id}', [SecurityActionsController::class, 'unlockUserAccount'])->name('unlock-user');
            Route::post('/generate-report', [SecurityActionsController::class, 'generateReport'])->name('generate-report');
            Route::post('/update-threat-db', [SecurityActionsController::class, 'updateThreatDatabase'])->name('update-threat-db');
        
            // Get data endpoints
            Route::get('/blocked-ips', [SecurityActionsController::class, 'getBlockedIps'])->name('blocked-ips');
            Route::get('/locked-users', [SecurityActionsController::class, 'getLockedUsers'])->name('locked-users');
            Route::get('/system-lock-status', [SecurityActionsController::class, 'getSystemLockStatus'])->name('system-lock-status');
        });
        
        // User search endpoint
        Route::get('/search-users', [SecurityActionsController::class, 'searchUsers'])->name('search-users');
    });
    
    // User profile
    Route::get('/profile/login-history', [AuditLogController::class, 'userHistory'])->name('profile.login-history');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// API Routes for security dashboard (AJAX calls)
Route::middleware(['auth', 'security'])->prefix('api/security')->name('api.security.')->group(function () {
    Route::post('/lock-system', [SecurityActionsController::class, 'lockSystem']);
    Route::post('/unlock-system', [SecurityActionsController::class, 'unlockSystem']);
    Route::post('/block-ip', [SecurityActionsController::class, 'blockIpAddress']);
    Route::get('/blocked-ips', [SecurityActionsController::class, 'getBlockedIps']);
    Route::post('/require-2fa', [SecurityActionsController::class, 'require2FA']);
    Route::post('/lock-user', [SecurityActionsController::class, 'lockUserAccount']);
    Route::get('/locked-users', [SecurityActionsController::class, 'getLockedUsers']);
    Route::post('/generate-report', [SecurityActionsController::class, 'generateReport']);
    Route::post('/update-threat-db', [SecurityActionsController::class, 'updateThreatDatabase']);
    Route::get('/system-lock-status', [SecurityActionsController::class, 'getSystemLockStatus']);
    
    // User search API
    Route::get('/users/search', [SecurityActionsController::class, 'searchUsers'])->name('users.search');
});

require __DIR__.'/auth.php';