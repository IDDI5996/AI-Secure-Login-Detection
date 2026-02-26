<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThreatDatabase extends Model
{
    protected $table = 'threat_database';
    
    protected $fillable = [
        'threat_type',
        'threat_data',
        'action',
        'added_by',
        'is_active'
    ];
    
    protected $casts = [
        'threat_data' => 'array',
        'is_active' => 'boolean'
    ];
    
    public function adder()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
