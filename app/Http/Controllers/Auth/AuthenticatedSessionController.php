<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\LoginAttempt;
use App\Models\VerificationAttempt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Stevebauman\Location\Facades\Location;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        Log::info('🔐 Login process started', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'time' => now()->toISOString()
        ]);

        // Get location data
        $locationData = $this->getLocationData($request->ip());
        
        // Get device data
        $deviceData = $this->getDeviceData($request->userAgent());

        // Create login attempt BEFORE authentication
        $loginAttempt = LoginAttempt::create([
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'country' => $locationData['country'] ?? null,
            'city' => $locationData['city'] ?? null,
            'browser' => $deviceData['browser'] ?? null,
            'platform' => $deviceData['platform'] ?? null,
            'device_type' => $deviceData['device_type'] ?? null,
            'is_successful' => false,
            'is_suspicious' => false,
            'risk_score' => 0.5,
            'attempted_at' => now(),
        ]);

        Log::info('📝 Login attempt record created', [
            'attempt_id' => $loginAttempt->id,
            'email' => $loginAttempt->email,
            'ip' => $loginAttempt->ip_address
        ]);

        try {
            // Attempt authentication
            $request->authenticate();

            // Get the authenticated user
            $user = Auth::user();
            
            Log::info('✅ Authentication successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            // Calculate risk score (you can customize this)
            $riskScore = $this->calculateRiskScore($user, $loginAttempt);

            // Update login attempt with user info and risk assessment
            $loginAttempt->update([
                'user_id' => $user->id,
                'is_successful' => true,
                'risk_score' => $riskScore,
                'is_suspicious' => $riskScore >= 0.7, // Mark as suspicious if high risk
                'detection_factors' => $this->getDetectionFactors($user, $loginAttempt, $riskScore)
            ]);

            Log::info('📊 Login attempt updated', [
                'attempt_id' => $loginAttempt->id,
                'user_id' => $user->id,
                'risk_score' => $riskScore,
                'is_suspicious' => $loginAttempt->is_suspicious
            ]);

            // Create verification attempt
            $verificationAttempt = VerificationAttempt::create([
                'user_id' => $user->id,
                'login_attempt_id' => $loginAttempt->id,
                'verification_method' => 'password_only',
                'is_successful' => true,
                'verification_data' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'location' => $locationData['location_string'] ?? null,
                    'device' => $deviceData['device_string'] ?? null,
                    'method' => 'standard_login',
                    'session_id' => session()->getId(),
                    'timestamp' => now()->toISOString(),
                    'risk_assessment' => [
                        'score' => $riskScore,
                        'is_suspicious' => $loginAttempt->is_suspicious
                    ]
                ],
                'verified_at' => now()
            ]);

            Log::info('✅ Verification attempt created', [
                'verification_id' => $verificationAttempt->id,
                'login_attempt_id' => $loginAttempt->id,
                'method' => $verificationAttempt->verification_method
            ]);

            // Clear rate limiter
            RateLimiter::clear($request->throttleKey());

            // Regenerate session
            $request->session()->regenerate();

            Log::info('🔄 Session regenerated, redirecting to dashboard', [
                'user_id' => $user->id,
                'session_id' => session()->getId()
            ]);

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (ValidationException $e) {
            // Update login attempt as failed
            $loginAttempt->update([
                'is_successful' => false,
                'risk_score' => 0.8, // High risk for failed
                'detection_factors' => ['failed_authentication']
            ]);

            Log::warning('❌ Authentication failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'attempt_id' => $loginAttempt->id,
                'errors' => $e->errors()
            ]);
            
            throw $e;
            
        } catch (\Exception $e) {
            // Update login attempt on any other error
            $loginAttempt->update([
                'is_successful' => false,
                'risk_score' => 0.9,
                'detection_factors' => ['system_error', $e->getMessage()]
            ]);

            Log::error('💥 Login process error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'attempt_id' => $loginAttempt->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get location data from IP
     */
    private function getLocationData($ip)
    {
        try {
            // Skip local IPs
            if ($ip === '127.0.0.1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.0.') === 0) {
                return [
                    'country' => 'Local',
                    'city' => 'Local Network',
                    'location_string' => 'Local Network'
                ];
            }

            $location = Location::get($ip);
            
            if ($location) {
                return [
                    'country' => $location->countryName ?? null,
                    'city' => $location->cityName ?? null,
                    'location_string' => ($location->cityName ?? 'Unknown') . ', ' . ($location->countryName ?? 'Unknown')
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get location data', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return [
            'country' => 'Unknown',
            'city' => 'Unknown',
            'location_string' => 'Unknown Location'
        ];
    }

    /**
     * Get device data from user agent
     */
    private function getDeviceData($userAgent)
    {
        try {
            // Set up the device detector
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $dd = new DeviceDetector($userAgent);
            $dd->parse();

            $deviceType = 'Unknown';
            if ($dd->isBot()) {
                $deviceType = 'Bot';
            } elseif ($dd->isDesktop()) {
                $deviceType = 'Desktop';
            } elseif ($dd->isTablet()) {
                $deviceType = 'Tablet';
            } elseif ($dd->isMobile()) {
                $deviceType = 'Mobile';
            }

            return [
                'browser' => $dd->getClient('name') ?? 'Unknown',
                'platform' => $dd->getOs('name') ?? 'Unknown',
                'device_type' => $deviceType,
                'device_string' => ($deviceType !== 'Unknown' ? $deviceType . ' - ' : '') . 
                                 ($dd->getClient('name') ?? 'Unknown') . ' on ' . 
                                 ($dd->getOs('name') ?? 'Unknown')
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to parse device data', ['user_agent' => $userAgent, 'error' => $e->getMessage()]);
            
            return [
                'browser' => 'Unknown',
                'platform' => 'Unknown',
                'device_type' => 'Unknown',
                'device_string' => 'Unknown Device'
            ];
        }
    }

    /**
     * Calculate risk score for login attempt
     */
    private function calculateRiskScore($user, $loginAttempt)
    {
        $score = 0.1; // Base score for successful login
        
        // Check if unusual location (simplified)
        $previousLogin = LoginAttempt::where('user_id', $user->id)
            ->where('is_successful', true)
            ->whereNotNull('country')
            ->latest('attempted_at')
            ->skip(1) // Skip the current one
            ->first();
            
        if ($previousLogin && $previousLogin->country !== $loginAttempt->country) {
            $score += 0.3; // Location change adds risk
        }
        
        // Check if unusual device
        $previousDevice = LoginAttempt::where('user_id', $user->id)
            ->where('is_successful', true)
            ->whereNotNull('device_type')
            ->latest('attempted_at')
            ->skip(1)
            ->first();
            
        if ($previousDevice && $previousDevice->device_type !== $loginAttempt->device_type) {
            $score += 0.2; // Device change adds risk
        }
        
        // Cap at 0.95
        return min($score, 0.95);
    }

    /**
     * Get detection factors for suspicious activity
     */
    private function getDetectionFactors($user, $loginAttempt, $riskScore)
    {
        $factors = [];
        
        if ($riskScore >= 0.7) {
            $factors[] = 'high_risk_login';
        }
        
        // Check time of day (simplified - you can add more logic)
        $hour = now()->hour;
        if ($hour < 6 || $hour > 22) {
            $factors[] = 'unusual_login_time';
        }
        
        // Check if first login
        $loginCount = LoginAttempt::where('user_id', $user->id)
            ->where('is_successful', true)
            ->count();
            
        if ($loginCount <= 1) {
            $factors[] = 'first_login';
        }
        
        return empty($factors) ? null : $factors;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $userEmail = Auth::user()?->email;
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('👋 User logged out', [
            'user_id' => $userId,
            'email' => $userEmail,
            'time' => now()->toISOString()
        ]);

        return redirect('/');
    }
}