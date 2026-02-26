<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    protected $fillable = [
        'ip_address',
        'blocked_by',
        'reason',
        'blocked_at',
        'unblocks_at',
        'unblocked_at',
        'unblocked_by',
        'is_active'
    ];
    
    protected $casts = [
        'blocked_at' => 'datetime',
        'unblocks_at' => 'datetime',
        'unblocked_at' => 'datetime',
        'is_active' => 'boolean'
    ];
    
    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }
    
    public function unblocker()
    {
        return $this->belongsTo(User::class, 'unblocked_by');
    }
}
