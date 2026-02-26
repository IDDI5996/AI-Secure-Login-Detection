<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuspiciousActivity extends Model
{
    protected $fillable = [
        'user_id', 'activity_type', 'activity_data',
        'risk_score', 'detection_reasons', 'status',
        'reviewed_by', 'reviewed_at', 'review_notes'
    ];

    protected $casts = [
        'activity_data' => 'array',
        'detection_reasons' => 'array',
        'reviewed_at' => 'datetime',
        'risk_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Activity types
    const TYPE_LOGIN = 'login_attempt';
    const TYPE_PASSWORD_CHANGE = 'password_change';
    const TYPE_EMAIL_CHANGE = 'email_change';
    const TYPE_2FA_DISABLE = '2fa_disable';
    
    // Statuses
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_FALSE_POSITIVE = 'false_positive';
}
