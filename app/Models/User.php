<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_super_admin',
        'role',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'security_questions',
        'trusted_devices',
        'login_notifications',
        
        'is_locked',
        'locked_at',
        'locked_by',
        'lock_reason',
        'unlocks_at',
        'unlocked_at',
        'unlocked_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'security_questions',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'two_factor_confirmed_at' => 'datetime',
            'security_questions' => 'array',
            'trusted_devices' => 'array',
            'login_notifications' => 'boolean',
            
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
            'unlocks_at' => 'datetime',
            'unlocked_at' => 'datetime',
        ];
    }

    /**
     * Relationship with login attempts
     */
    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class);
    }

    /**
     * Relationship with suspicious activities
     */
    public function suspiciousActivities()
    {
        return $this->hasMany(SuspiciousActivity::class);
    }

    /**
     * Relationship with behavior profile
     */
    public function behaviorProfile()
    {
        return $this->hasOne(UserBehaviorProfile::class);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->is_super_admin;
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    /**
     * Get the user's role with fallback
     */
    public function getRoleAttribute($value)
    {
        // Check if we have a database value first
        if (!empty($value)) {
            return $value;
        }
    
        // Fallback to old logic if no database role
        if ($this->is_super_admin) {
            return 'super_admin';
        }
    
        if ($this->is_admin) {
            return 'admin';
        }
    
        return 'user';
    }

    /**
     * Add a trusted device
     */
    public function addTrustedDevice(array $deviceInfo): void
    {
        $trustedDevices = $this->trusted_devices ?? [];
        $deviceId = md5(json_encode($deviceInfo));
        
        $trustedDevices[$deviceId] = [
            'device_info' => $deviceInfo,
            'added_at' => now()->toDateTimeString(),
            'last_used' => now()->toDateTimeString(),
        ];
        
        $this->trusted_devices = $trustedDevices;
        $this->save();
    }

    /**
     * Check if device is trusted
     */
    public function isDeviceTrusted(array $deviceInfo): bool
    {
        if (empty($this->trusted_devices)) {
            return false;
        }
        
        $deviceId = md5(json_encode($deviceInfo));
        return isset($this->trusted_devices[$deviceId]);
    }

    /**
     * Update last used time for trusted device
     */
    public function updateDeviceLastUsed(array $deviceInfo): void
    {
        if ($this->isDeviceTrusted($deviceInfo)) {
            $deviceId = md5(json_encode($deviceInfo));
            $this->trusted_devices[$deviceId]['last_used'] = now()->toDateTimeString();
            $this->save();
        }
    }

    /**
     * Get recent suspicious activities
     */
    public function recentSuspiciousActivities($limit = 10)
    {
        return $this->suspiciousActivities()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent login attempts
     */
    public function recentLoginAttempts($limit = 10)
    {
        return $this->loginAttempts()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Check if user has security questions set up
     */
    public function hasSecurityQuestions(): bool
    {
        return !empty($this->security_questions) && 
               is_array($this->security_questions) && 
               count($this->security_questions) >= 2;
    }

    /**
     * Verify security questions
     */
    public function verifySecurityQuestions(array $answers): bool
    {
        if (!$this->hasSecurityQuestions()) {
            return false;
        }
        
        foreach ($answers as $questionId => $answer) {
            if (!isset($this->security_questions[$questionId]) || 
                strtolower(trim($this->security_questions[$questionId]['answer'])) !== strtolower(trim($answer))) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get formatted security questions (without answers)
     */
    public function getSecurityQuestionsFormatted(): array
    {
        if (!$this->hasSecurityQuestions()) {
            return [];
        }
        
        $questions = [];
        foreach ($this->security_questions as $id => $qa) {
            $questions[$id] = [
                'question' => $qa['question'],
                'hint' => $qa['hint'] ?? null,
            ];
        }
        
        return $questions;
    }
    
    public function locker()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function unlocker()
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }
}