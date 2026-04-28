<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use App\Services\AiDetectionEngin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // AI Detection Integration for web login
        Fortify::authenticateUsing(function (Request $request) {
            file_put_contents(storage_path('logs/fortify_test.txt'), now() . " - callback hit for {$request->email}\n", FILE_APPEND);

            $user = \App\Models\User::where('email', $request->email)->first();
            $aiEngine = app(AiDetectionEngin::class);

            $realIp = $request->ip();
            $browser = $this->parseBrowser($request->userAgent());
            $platform = $this->parsePlatform($request->userAgent());
            $deviceType = $this->parseDeviceType($request->userAgent());

            // Record login attempt (with email to avoid null constraint issues)
            $loginAttempt = LoginAttempt::create([
                'user_id' => $user?->id,
                'email' => $request->email,   // <-- ADDED THIS LINE
                'ip_address' => $realIp,
                'user_agent' => $request->userAgent(),
                'country' => null,
                'city' => null,
                'browser' => $browser,
                'platform' => $platform,
                'device_type' => $deviceType,
                'is_successful' => false,
                'attempted_at' => now(),
            ]);

            // Run AI analysis (handles null user)
            $analysis = $aiEngine->analyzeLoginAttempt($user ?? null, $request, $realIp);

            $loginAttempt->update([
                'risk_score' => $analysis['risk_score'],
                'is_suspicious' => $analysis['is_suspicious'],
                'detection_factors' => $analysis['detection_factors'],
            ]);

            Log::info('Login attempt updated', [
                'id' => $loginAttempt->id,
                'risk_score' => $analysis['risk_score'],
                'is_suspicious' => $analysis['is_suspicious'],
            ]);

            // Credential check
            if (!$user || !Hash::check($request->password, $user->password)) {
                $loginAttempt->update(['is_successful' => false]);

                if ($analysis['is_suspicious']) {
                    SuspiciousActivity::create([
                        'user_id' => $user?->id,
                        'activity_type' => SuspiciousActivity::TYPE_LOGIN,
                        'activity_data' => [
                            'login_attempt_id' => $loginAttempt->id,
                            'ip_address' => $loginAttempt->ip_address,
                            'email_attempted' => $request->email,
                            'location' => "{$loginAttempt->city}, {$loginAttempt->country}",
                            'device' => $loginAttempt->device_type,
                            'is_successful' => false,
                        ],
                        'risk_score' => $analysis['risk_score'],
                        'detection_reasons' => $analysis['reasons'],
                        'status' => SuspiciousActivity::STATUS_PENDING,
                    ]);
                }

                return null; // Fortify handles the error
            }

            // Successful login
            $loginAttempt->update(['is_successful' => true]);

            return $user;
        });
    }

    private function parseBrowser($userAgent): string
    {
        if (str_contains($userAgent, 'Chrome')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Safari';
        if (str_contains($userAgent, 'Edge')) return 'Edge';
        return 'Unknown';
    }

    private function parsePlatform($userAgent): string
    {
        if (str_contains($userAgent, 'Windows')) return 'Windows';
        if (str_contains($userAgent, 'Mac')) return 'macOS';
        if (str_contains($userAgent, 'Linux')) return 'Linux';
        if (str_contains($userAgent, 'Android')) return 'Android';
        if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) return 'iOS';
        return 'Unknown';
    }

    private function parseDeviceType($userAgent): string
    {
        if (str_contains($userAgent, 'Mobile')) return 'Mobile';
        if (str_contains($userAgent, 'Tablet')) return 'Tablet';
        return 'Desktop';
    }
}