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

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
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

        // -- AI Detection Integration for web login --
        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)->first();
            $aiEngine = app(AiDetectionEngin::class);

            // Get IP (using simple $request->ip() – you can enhance with real‑IP header logic later)
            $realIp = $request->ip();

            // Basic browser/device parsing (optional but helpful)
            $browser = $this->parseBrowser($request->userAgent());
            $platform = $this->parsePlatform($request->userAgent());
            $deviceType = $this->parseDeviceType($request->userAgent());

            // Record a fresh login attempt
            $loginAttempt = LoginAttempt::create([
                'user_id' => $user?->id,
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

            // Run AI analysis (works with or without a user)
            $analysis = $aiEngine->analyzeLoginAttempt($user ?? null, $request, $realIp);

            $loginAttempt->update([
                'risk_score' => $analysis['risk_score'],
                'is_suspicious' => $analysis['is_suspicious'],
                'detection_factors' => $analysis['detection_factors'],
            ]);

            // If credentials are wrong
            if (!$user || !Hash::check($request->password, $user->password)) {
                $loginAttempt->update(['is_successful' => false]);

                // If the failed attempt is suspicious, create a SuspiciousActivity
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

                // Return null to let Fortify handle the "invalid credentials" error
                return null;
            }

            // Successful login
            $loginAttempt->update(['is_successful' => true]);

            // If the successful login is suspicious, you could also create a SuspiciousActivity
            // (as the AiLoginController does). For now we simply allow the login.
            // You can extend it to require verification later.

            return $user;
        });
    }

    /**
     * Simple browser parser (mirrors AiDetectionEngin).
     */
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