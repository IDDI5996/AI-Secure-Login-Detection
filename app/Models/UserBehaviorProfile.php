<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBehaviorProfile extends Model
{
    protected $fillable = [
        'user_id', 'usual_locations', 'usual_times',
        'device_fingerprints', 'typing_pattern',
        'mouse_movement_patterns', 'login_count'
    ];

    protected $casts = [
        'usual_locations' => 'array',
        'usual_times' => 'array',
        'device_fingerprints' => 'array',
        'typing_pattern' => 'array',
        'mouse_movement_patterns' => 'array',
        'last_updated' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateLoginPattern($loginData)
    {
        $this->login_count++;
        
        // Update usual locations
        $locations = $this->usual_locations ?? [];
        $newLocation = [
            'country' => $loginData['country'],
            'city' => $loginData['city'],
            'ip_range' => substr($loginData['ip_address'], 0, strrpos($loginData['ip_address'], '.')),
            'last_used' => now()->toDateTimeString(),
            'count' => 1
        ];
        
        // Check if location exists and update
        $found = false;
        foreach ($locations as &$location) {
            if ($location['ip_range'] === $newLocation['ip_range']) {
                $location['count']++;
                $location['last_used'] = $newLocation['last_used'];
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $locations[] = $newLocation;
        }
        
        // Update usual times
        $times = $this->usual_times ?? ['hour_ranges' => [], 'days' => []];
        $currentHour = now()->hour;
        $currentDay = now()->dayOfWeek;
        
        if (!in_array($currentHour, $times['hour_ranges'])) {
            $times['hour_ranges'][] = $currentHour;
            sort($times['hour_ranges']);
        }
        
        if (!in_array($currentDay, $times['days'])) {
            $times['days'][] = $currentDay;
            sort($times['days']);
        }
        
        $this->usual_locations = $locations;
        $this->usual_times = $times;
        $this->last_updated = now();
        $this->save();
    }
}
