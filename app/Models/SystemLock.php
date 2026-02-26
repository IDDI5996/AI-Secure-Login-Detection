<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLock extends Model
{
    protected $fillable = [
        'locked_by',
        'reason',
        'locked_at',
        'unlocks_at',
        'unlocked_at',
        'unlocked_by',
        'is_active'
    ];
    
    protected $casts = [
        'locked_at' => 'datetime',
        'unlocks_at' => 'datetime',
        'unlocked_at' => 'datetime',
        'is_active' => 'boolean'
    ];
    
    public function locker()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
    
    public function unlocker()
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }
}
