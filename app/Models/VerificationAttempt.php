<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_attempt_id',
        'verification_method',
        'is_successful',
        'verification_data',
        'verified_at'
    ];

    protected $casts = [
        'is_successful' => 'boolean',
        'verification_data' => 'array',
        'verified_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loginAttempt()
    {
        return $this->belongsTo(LoginAttempt::class);
    }
}