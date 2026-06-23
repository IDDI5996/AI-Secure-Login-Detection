<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBehaviorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'usual_locations',
        'usual_times',
        'device_fingerprints',
        'typing_pattern',
        'mouse_movement_patterns',
        'login_count',
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

    public function updateLoginPattern(array $loginData): void
    {
        $this->login_count++;

        // Safely retrieve location data, ensuring it's an array
        $locations = $this->getAttributeAsArray('usual_locations');
        $times = $this->getAttributeAsArray('usual_times');

        // Build new location entry
        $newLocation = [
            'country' => $loginData['country'] ?? 'Unknown',
            'city'    => $loginData['city'] ?? 'Unknown',
            'ip_range' => $this->extractIpRange($loginData['ip_address'] ?? ''),
            'last_used' => now()->toDateTimeString(),
            'count' => 1,
        ];

        // Update or add location
        $found = false;
        foreach ($locations as &$location) {
            if (($location['ip_range'] ?? null) === $newLocation['ip_range']) {
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
        $currentHour = now()->hour;
        $currentDay = now()->dayOfWeek;

        if (!in_array($currentHour, $times['hour_ranges'] ?? [])) {
            $times['hour_ranges'][] = $currentHour;
            sort($times['hour_ranges']);
        }

        if (!in_array($currentDay, $times['days'] ?? [])) {
            $times['days'][] = $currentDay;
            sort($times['days']);
        }

        $this->usual_locations = $locations;
        $this->usual_times = $times;
        $this->last_updated = now();
        $this->save();
    }

    /**
     * Helper to safely get an array from an attribute,
     * handling both null and JSON string values.
     */
    private function getAttributeAsArray(string $key): array
    {
        $value = $this->getAttribute($key);

        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        // If it's a JSON string, decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        // Fallback: return an empty array
        return [];
    }

    /**
     * Helper to extract the first three octets of an IP address.
     */
    private function extractIpRange(string $ip): string
    {
        $parts = explode('.', $ip);
        if (count($parts) >= 3) {
            return implode('.', array_slice($parts, 0, 3));
        }
        return $ip;
    }
}