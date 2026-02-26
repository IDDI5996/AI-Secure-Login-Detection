<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    protected $fillable = [
        'user_id', 'email', 'ip_address', 'user_agent', 'country', 'city',
        'browser', 'platform', 'device_type', 'is_successful',
        'is_suspicious', 'risk_score', 'detection_factors', 'attempted_at'
    ];

    protected $casts = [
        'detection_factors' => 'array',
        'attempted_at' => 'datetime',
        'is_suspicious' => 'boolean',
        'is_successful' => 'boolean',
        'risk_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verificationAttempt()
    {
        return $this->hasOne(VerificationAttempt::class);
    }

    // Scope for suspicious attempts
    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    // Scope for recent attempts
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('attempted_at', '>=', now()->subHours($hours));
    }
    
    public function getLocationAttribute()
{
    $location = [];
    if ($this->city) {
        $location[] = $this->city;
    }
    if ($this->country) {
        $location[] = $this->country;
    }
    
    return !empty($location) ? implode(', ', $location) : 'Unknown';
}
}
