<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AiFeatureExtractor
{
    public function extract(User $user, Request $request): array
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $now = Carbon::now();
        
        // Parse location from IP (using cached geolocation)
        $location = $this->getLocationFromIp($ip);
        
        // Get user's recent attempts (last 24 hours)
        $recentAttempts = LoginAttempt::where('user_id', $user->id)
            ->where('attempted_at', '>=', $now->copy()->subHours(24))
            ->get();
        
        $totalAttempts = $recentAttempts->count();
        $failedAttempts = $recentAttempts->where('is_successful', false)->count();
        $successfulAttempts = $recentAttempts->where('is_successful', true)->count();
        
        // Calculate features
        $features = [
            // Status features
            'status' => 1, // 1 for login attempt
            
            // Time features
            'hour' => (int) $now->format('H'),
            'day_of_week' => (int) $now->format('N'),
            'minute' => (int) $now->format('i'),
            'is_weekend' => $now->isWeekend() ? 1 : 0,
            
            // Login velocity features
            'login_count_24h' => $totalAttempts,
            'failed_count_24h' => $failedAttempts,
            'fail_rate_24h' => $totalAttempts > 0 ? $failedAttempts / $totalAttempts : 0,
            
            // Location features
            'unique_ips_24h' => $recentAttempts->pluck('ip_address')->unique()->count(),
            'unique_countries_24h' => $recentAttempts->pluck('country')->unique()->count(),
            
            // Device features
            'unique_devices_24h' => $recentAttempts->pluck('device_type')->unique()->count(),
            
            // Frequency features (global)
            'ip_freq' => LoginAttempt::where('ip_address', $ip)->count(),
            'country_freq' => LoginAttempt::where('country', $location['country'] ?? 'Unknown')->count(),
            'device_type_freq' => LoginAttempt::where('device_type', $this->parseDeviceType($userAgent))->count(),
            'browser_freq' => LoginAttempt::where('browser', $this->parseBrowser($userAgent))->count(),
        ];
        
        return $features;
    }
    
    private function getLocationFromIp(string $ip): array
    {
        try {
            $location = \Stevebauman\Location\Facades\Location::get($ip);
            return [
                'country' => $location->countryName ?? 'Unknown',
                'city' => $location->cityName ?? 'Unknown',
            ];
        } catch (\Exception $e) {
            return ['country' => 'Unknown', 'city' => 'Unknown'];
        }
    }
    
    private function parseDeviceType(string $userAgent): string
    {
        if (str_contains($userAgent, 'Mobile')) {
            return 'Mobile';
        }
        if (str_contains($userAgent, 'Tablet')) {
            return 'Tablet';
        }
        return 'Desktop';
    }
    
    private function parseBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Chrome') && !str_contains($userAgent, 'Edg')) {
            return 'Chrome';
        }
        if (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        }
        if (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome')) {
            return 'Safari';
        }
        if (str_contains($userAgent, 'Edg')) {
            return 'Edge';
        }
        return 'Unknown';
    }
}