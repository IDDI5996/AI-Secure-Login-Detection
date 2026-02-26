<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Different dashboard based on role
        if ($user->role === 'super_admin') {
            return $this->superAdminDashboard($user);
        } 
        elseif ($user->role === 'security_lead') {
            return $this->securityLeadDashboard($user);
        } 
        elseif ($user->role === 'security_analyst') {
            return $this->securityAnalystDashboard($user);
        } 
        else {
            return $this->userDashboard($user);
        }
    }

    private function superAdminDashboard($user)
    {
        // Redirect to resources/views/dashboard.blade.php
        return view('dashboard'); // Directly to dashboard.blade.php in root of views folder
    }

    private function securityLeadDashboard($user)
    {
        $today = Carbon::today();
        
        $stats = [
            'todayLogins' => LoginAttempt::whereDate('attempted_at', $today)->count(),
            'suspiciousAttempts' => LoginAttempt::whereDate('attempted_at', $today)
                ->where('is_suspicious', true)
                ->count(),
            'avgRiskScore' => LoginAttempt::whereDate('attempted_at', $today)
                ->avg('risk_score') * 100 ?? 0,
            'pendingReviews' => SuspiciousActivity::where('status', 'pending')->count(),
            'totalUsers' => User::count(),
            'activeUsers' => User::where('last_login_at', '>=', $today->subDays(7))->count(),
            'failedLogins' => LoginAttempt::whereDate('attempted_at', $today)
                ->where('is_successful', false)
                ->count(),
            'uniqueLocations' => LoginAttempt::whereDate('attempted_at', $today)
                ->distinct('country')
                ->count('country'),
        ];

        // Redirect to resources/views/admin/dashboard.blade.php
        return view('admin.dashboard', compact('stats'));
    }

    private function securityAnalystDashboard($user)
    {
        $today = Carbon::today();
        
        $stats = [
            'suspiciousAttempts' => LoginAttempt::whereDate('attempted_at', $today)
                ->where('is_suspicious', true)
                ->count(),
            'pendingReviews' => SuspiciousActivity::where('status', 'pending')->count(),
            'highRiskActivities' => SuspiciousActivity::where('risk_score', '>=', 0.8)
                ->where('status', 'pending')
                ->count(),
            'recentAlerts' => SuspiciousActivity::where('created_at', '>=', $today->subHours(24))
                ->count(),
        ];

        // Redirect to resources/views/security/dashboard.blade.php
        return view('security.dashboard', compact('stats'));
    }

    private function userDashboard($user)
    {
        $recentLogins = LoginAttempt::where('user_id', $user->id)
            ->orderBy('attempted_at', 'desc')
            ->limit(10)
            ->get();

        $suspiciousCount = LoginAttempt::where('user_id', $user->id)
            ->where('is_suspicious', true)
            ->whereDate('attempted_at', '>=', Carbon::today()->subDays(30))
            ->count();

        $devices = $user->behaviorProfile->device_fingerprints ?? [];

        // Redirect to resources/views/user/dashboard.blade.php
        return view('user.dashboard', compact('recentLogins', 'suspiciousCount', 'devices'));
    }
}