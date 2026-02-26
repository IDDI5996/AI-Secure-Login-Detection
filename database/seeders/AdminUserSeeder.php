<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\UserBehaviorProfile;
use App\Models\SuspiciousActivity;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    private $faker;
    
    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }
    
    public function run(): void
    {
        $this->command->info('🚀 Starting demo data generation...');
        
        // 1. Create Users
        $this->createUsers();
        
        // 2. Create Behavior Profiles
        $this->createBehaviorProfiles();
        
        // 3. Create Login History
        $this->createLoginHistory();
        
        // 4. Create Suspicious Activities
        $this->createSuspiciousActivities();
        
        $this->displaySummary();
    }
    
    private function createUsers(): void
    {
        $users = [
            // Super Admin
            [
                'name' => 'Zinfaiyang',
                'email' => 'zinfaiyang@gmail.com',
                'password' => Hash::make('SuperSecure@123'),
                'is_admin' => true,
                'is_super_admin' => true,
                'role' => 'super_admin',
                'two_factor_enabled' => true,
                'email_verified_at' => now(),
            ],
            // Security Team
            [
                'name' => 'Iddi Hemedi',
                'email' => 'iddihemedi11@gmail.com',
                'password' => Hash::make('SecurityLead@456'),
                'is_admin' => true,
                'role' => 'security_lead',
                'two_factor_enabled' => true,
                'email_verified_at' => now(),
            ],
            // Security Analyst
            [
                'name' => 'Modestus Ngimba',
                'email' => 'modestusngimba@gmail.com',
                'password' => Hash::make('Analyst@789'),
                'is_admin' => true,
                'role' => 'security_analyst',
                'two_factor_enabled' => true,
                'email_verified_at' => now(),
            ],
            // Regular users with different patterns
            [
                'name' => 'Daniel Mosamba',
                'email' => 'danielmosamba@gmail.com',
                'password' => Hash::make('DanielPass@123'),
                'two_factor_enabled' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Restituta Qambesh',
                'email' => 'restitutaqambesh@gmail.com',
                'password' => Hash::make('RestitutaPass@123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Radhia Kijida',
                'email' => 'radhiakijida@gmail.com',
                'password' => Hash::make('RadhiaPass@123'),
                'two_factor_enabled' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Naomi Misungwi',
                'email' => 'naomimisungwi@gmail.com',
                'password' => Hash::make('NaomiPass@123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Godfrey Godson',
                'email' => 'godfreygodson@gmail.com',
                'password' => Hash::make('GodfreyPass123!'),
                'two_factor_enabled' => true,
                'email_verified_at' => now(),
            ],
        ];
        
        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'created_at' => now()->subMonths(rand(1, 6)),
                    'updated_at' => now(),
                ])
            );
        }
        
        $this->command->info("✅ Created " . count($users) . " users");
    }
    
    private function createBehaviorProfiles(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            UserBehaviorProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'usual_locations' => $this->generateUsualLocations($user),
                    'usual_times' => $this->generateUsualTimes($user),
                    'device_fingerprints' => $this->generateDeviceFingerprints($user),
                    'typing_pattern' => $this->generateTypingPattern($user),
                    'mouse_movement_patterns' => $this->generateMousePatterns($user),
                    'login_count' => rand(15, 150),
                    'last_updated' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        $this->command->info("✅ Created behavior profiles for all users");
    }
    
    private function createLoginHistory(): void
    {
        $users = User::all();
        $loginAttempts = [];
        
        // Generate 30 days of login history
        for ($day = 30; $day >= 0; $day--) {
            $date = now()->subDays($day);
            
            // Generate daily logins
            $dailyLogins = rand(5, 20);
            
            for ($i = 0; $i < $dailyLogins; $i++) {
                $user = $users->random();
                $isSuspicious = $this->shouldBeSuspicious($user, $date);
                
                $loginAttempts[] = [
                    'user_id' => $user->id,
                    'ip_address' => $this->generateIP($user),
                    'user_agent' => $this->generateUserAgent($user),
                    'country' => $this->generateCountry($user),
                    'city' => $this->generateCity($user),
                    'browser' => $this->generateBrowser($user),
                    'platform' => $this->generatePlatform($user),
                    'device_type' => $this->generateDeviceType($user),
                    'is_successful' => rand(1, 20) !== 1, // 95% success rate
                    'is_suspicious' => $isSuspicious,
                    'risk_score' => $isSuspicious ? rand(70, 95) / 100 : rand(5, 60) / 100,
                    'detection_factors' => $isSuspicious ? $this->generateDetectionFactors() : null,
                    'attempted_at' => $date->copy()
                        ->setHour(rand(6, 22))
                        ->setMinute(rand(0, 59))
                        ->setSecond(rand(0, 59)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Insert in chunks
        foreach (array_chunk($loginAttempts, 100) as $chunk) {
            LoginAttempt::insert($chunk);
        }
        
        $this->command->info("✅ Generated " . count($loginAttempts) . " login attempts");
    }
    
    private function createSuspiciousActivities(): void
{
    $suspiciousLogins = LoginAttempt::where('is_suspicious', true)->get();
    $admins = User::where('is_admin', true)->get();
    $activities = [];
    
    foreach ($suspiciousLogins as $index => $login) {
        $status = $this->determineActivityStatus($index);
        $reviewer = $status !== 'pending' ? $admins->random()->id : null;
        
        // Parse detection_factors if it's a JSON string
        $riskFactors = $login->detection_factors;
        if (is_string($riskFactors) && !empty($riskFactors)) {
            $riskFactors = json_decode($riskFactors, true);
        }
        
        $activities[] = [
            'user_id' => $login->user_id,
            'activity_type' => 'login_attempt',
            'activity_data' => json_encode([
                'login_attempt_id' => $login->id,
                'ip_address' => $login->ip_address,
                'location' => "{$login->city}, {$login->country}",
                'device' => $login->device_type,
                'browser' => $login->browser,
                'timestamp' => $login->attempted_at->toDateTimeString(),
                'risk_factors' => $riskFactors, // Use the parsed/raw value
            ]),
            'risk_score' => $login->risk_score,
            'detection_reasons' => json_encode($this->generateDetectionReasons()),
            'status' => $status,
            'reviewed_by' => $reviewer,
            'reviewed_at' => $reviewer ? now()->subDays(rand(0, 7)) : null,
            'review_notes' => $reviewer ? $this->generateReviewNotes($status) : null,
            'created_at' => $login->attempted_at,
            'updated_at' => now(),
        ];
    }
    
    foreach (array_chunk($activities, 50) as $chunk) {
        SuspiciousActivity::insert($chunk);
    }
    
    $this->command->info("✅ Created " . count($activities) . " suspicious activities");
}
    
    // Helper methods for generating realistic data
    private function generateUsualLocations($user): string
    {
        $locations = [];
        
        // Main location (70% of logins)
        $locations[] = [
            'country' => 'US',
            'city' => $this->faker->randomElement(['New York', 'San Francisco', 'Chicago', 'Los Angeles']),
            'ip_range' => $this->faker->ipv4(),
            'last_used' => now()->subDays(rand(0, 3))->toDateTimeString(),
            'count' => rand(30, 100),
        ];
        
        // Secondary location (20% of logins)
        if (rand(1, 2) === 1) {
            $locations[] = [
                'country' => 'US',
                'city' => $this->faker->randomElement(['Boston', 'Seattle', 'Austin', 'Denver']),
                'ip_range' => $this->faker->ipv4(),
                'last_used' => now()->subDays(rand(7, 30))->toDateTimeString(),
                'count' => rand(5, 20),
            ];
        }
        
        // Travel location (10% of logins)
        if (rand(1, 5) === 1) {
            $locations[] = [
                'country' => $this->faker->randomElement(['UK', 'Germany', 'Canada', 'Australia']),
                'city' => $this->faker->city(),
                'ip_range' => $this->faker->ipv4(),
                'last_used' => now()->subDays(rand(60, 180))->toDateTimeString(),
                'count' => rand(1, 5),
            ];
        }
        
        return json_encode($locations);
    }
    
    private function generateUsualTimes($user): string
    {
        // Office workers: 9 AM - 5 PM
        if ($user->email !== 'robert.w@example.com') {
            return json_encode([
                'hour_ranges' => [9, 10, 11, 12, 13, 14, 15, 16, 17],
                'days' => [1, 2, 3, 4, 5], // Weekdays
            ]);
        }
        
        // Developer: unusual hours
        return json_encode([
            'hour_ranges' => [10, 11, 12, 14, 15, 16, 20, 21, 22],
            'days' => [1, 2, 3, 4, 5, 6], // Weekdays + Saturday
        ]);
    }
    
    private function shouldBeSuspicious($user, $date): bool
    {
        // Higher chance for certain users
        if ($user->email === 'robert.w@example.com') {
            return rand(1, 15) === 1; // 6.7% chance
        }
        
        // Normal users
        return rand(1, 30) === 1; // 3.3% chance
    }
    
    private function generateDetectionFactors(): string
{
    $factors = ['location', 'device', 'time_pattern', 'velocity', 'ip_reputation'];
    $selected = array_rand($factors, rand(2, 4));
    
    $result = [];
    foreach ((array)$selected as $factor) {
        $result[$factor] = [
            'weight' => match($factor) {
                'location' => 0.3,
                'device' => 0.25,
                'time_pattern' => 0.15,
                'velocity' => 0.2,
                'ip_reputation' => 0.1,
                default => 0.1,
            },
            'risk' => rand(60, 95) / 100,
            'data' => [
                'reason' => match($factor) {
                    'location' => 'Unusual geographic location detected',
                    'device' => 'New device fingerprint',
                    'time_pattern' => 'Login outside normal hours',
                    'velocity' => 'Multiple rapid login attempts',
                    'ip_reputation' => 'Suspicious IP reputation',
                    default => 'Unknown risk factor',
                }
            ]
        ];
    }
    
    return json_encode($result); // Ensure this returns JSON string
}
    
    private function determineActivityStatus($index): string
    {
        $statuses = ['pending', 'reviewed', 'resolved', 'false_positive'];
        $weights = [30, 40, 20, 10]; // Percentage distribution
        
        $total = array_sum($weights);
        $rand = rand(1, $total);
        
        $current = 0;
        foreach ($weights as $i => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $statuses[$i];
            }
        }
        
        return 'pending';
    }
    
    private function generateReviewNotes($status): string
    {
        $notes = [
            'reviewed' => [
                'Reviewed by security team. User confirmed legitimate access.',
                'Verified with user via email confirmation.',
                'Pattern matches known business travel schedule.',
            ],
            'resolved' => [
                'User confirmed this was their login attempt.',
                'Additional verification completed successfully.',
                'Risk mitigated with 2FA confirmation.',
            ],
            'false_positive' => [
                'AI model adjustment needed. This was legitimate activity.',
                'User behavior pattern updated to include this as normal.',
                'Geographic restriction too strict for this user.',
            ],
        ];
        
        return $notes[$status][array_rand($notes[$status])];
    }
    
    private function displaySummary(): void
    {
        $this->command->info('');
        $this->command->info('=========================================');
        $this->command->info('🎉 DEMO ENVIRONMENT READY!');
        $this->command->info('=========================================');
        $this->command->info('');
        
        $stats = [
            ['Total Users', User::count()],
            ['Admin Users', User::where('is_admin', true)->count()],
            ['2FA Enabled', User::where('two_factor_enabled', true)->count()],
            ['Login Attempts', LoginAttempt::count()],
            ['Suspicious Logins', LoginAttempt::where('is_suspicious', true)->count()],
            ['Suspicious Activities', SuspiciousActivity::count()],
            ['Pending Review', SuspiciousActivity::where('status', 'pending')->count()],
        ];
        
        $this->command->table(['Metric', 'Count'], $stats);
        
        $this->command->info('');
        $this->command->info('🔐 ADMIN ACCESS CREDENTIALS:');
        $this->command->info('─────────────────────────────');
        $this->command->info('Email: superadmin@aisecure.com');
        $this->command->info('Password: SuperSecure123!');
        $this->command->info('');
        $this->command->info('👤 REGULAR USER (with 2FA):');
        $this->command->info('─────────────────────────────');
        $this->command->info('Email: robert.w@example.com');
        $this->command->info('Password: RobertPass123!');
        $this->command->info('');
        $this->command->info('⚠️  SECURITY NOTICE:');
        $this->command->info('These are demo credentials. In production:');
        $this->command->info('1. Change all passwords immediately');
        $this->command->info('2. Enable actual 2FA for admin accounts');
        $this->command->info('3. Review and adjust security settings');
        $this->command->info('');
        $this->command->info('🚀 READY TO LAUNCH:');
        $this->command->info('Run: php artisan serve');
        $this->command->info('Visit: http://localhost:8000');
        $this->command->info('');
    }
    
    // Additional helper methods
    private function generateIP($user): string
    {
        $commonIPs = [
            '192.168.1.' . rand(100, 199),
            '10.0.0.' . rand(10, 99),
            '172.16.0.' . rand(10, 99),
        ];
        
        // Occasionally generate suspicious IPs
        if (rand(1, 20) === 1) {
            $suspiciousIPs = [
                '185.159.157.' . rand(1, 255), // Known VPN range
                '45.133.194.' . rand(1, 255),  // High risk country
                '103.21.244.' . rand(1, 255),  // Proxy server
            ];
            return $suspiciousIPs[array_rand($suspiciousIPs)];
        }
        
        return $commonIPs[array_rand($commonIPs)];
    }
    
    private function generateUserAgent($user): string
    {
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        ];
        
        return $agents[array_rand($agents)];
    }
    
    private function generateCountry($user): string
    {
        $common = ['US', 'US', 'US', 'US', 'US', 'UK', 'Canada', 'Germany']; // Weighted for US
        return $common[array_rand($common)];
    }
    
    private function generateCity($user): string
    {
        $cities = ['New York', 'San Francisco', 'Chicago', 'Los Angeles', 'Boston', 'Seattle'];
        return $cities[array_rand($cities)];
    }
    
    private function generateBrowser($user): string
    {
        return $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']);
    }
    
    private function generatePlatform($user): string
    {
        return $this->faker->randomElement(['Windows', 'macOS', 'iOS', 'Android']);
    }
    
    private function generateDeviceType($user): string
    {
        return $this->faker->randomElement(['Desktop', 'Mobile', 'Tablet']);
    }
    
    private function generateDeviceFingerprints($user): string
    {
        $fingerprints = [];
        
        // Primary device
        $fingerprints[] = [
            'fingerprint' => hash('sha256', $user->email . '-primary-device'),
            'browser' => 'Chrome',
            'platform' => $user->email === 'robert.w@example.com' ? 'macOS' : 'Windows',
            'device_type' => 'Desktop',
            'user_agent' => 'Mozilla/5.0 Chrome/120.0.0.0',
            'last_used' => now()->subDays(rand(0, 2))->toDateTimeString(),
        ];
        
        // Mobile device (60% of users)
        if (rand(1, 10) <= 6) {
            $fingerprints[] = [
                'fingerprint' => hash('sha256', $user->email . '-mobile-device'),
                'browser' => 'Safari',
                'platform' => 'iOS',
                'device_type' => 'Mobile',
                'user_agent' => 'Mozilla/5.0 iPhone Safari',
                'last_used' => now()->subDays(rand(3, 14))->toDateTimeString(),
            ];
        }
        
        return json_encode($fingerprints);
    }
    
    private function generateTypingPattern($user): string
    {
        return json_encode([
            'avg_keystroke_speed' => rand(45, 85),
            'common_errors' => ['teh' => 'the', 'adn' => 'and', 'recieve' => 'receive'],
            'preferred_keyboard_layout' => 'QWERTY',
            'typing_consistency' => rand(75, 95) . '%',
        ]);
    }
    
    private function generateMousePatterns($user): string
    {
        return json_encode([
            'avg_speed' => rand(120, 280),
            'common_trajectories' => ['direct', 's-curve', 'straight'],
            'click_frequency' => rand(3, 12),
            'scroll_behavior' => 'smooth',
        ]);
    }
    
    private function generateDetectionReasons(): array
    {
        $reasons = [
            'Login from unusual geographic location',
            'Unrecognized device fingerprint detected',
            'Login attempt outside normal hours',
            'Multiple failed attempts from same IP',
            'Suspicious IP reputation score',
            'Velocity attack pattern detected',
            'Behavioral anomaly in typing pattern',
            'Mouse movement pattern mismatch',
            'VPN/Proxy usage detected',
            'High risk country association',
        ];
        
        return array_rand(array_flip($reasons), rand(2, 4));
    }
}