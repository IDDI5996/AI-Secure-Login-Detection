<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuspiciousLoginDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $activity;

    /**
     * Create a new event instance.
     */
    public function __construct($activity)
    {
         $this->activity = $activity;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
     public function broadcastOn()
    {
        return new PrivateChannel('admin.suspicious-logins');
    }
    
    public function broadcastWith()
    {
        return [
            'id' => $this->activity->id,
            'user' => $this->activity->user->name,
            'risk_score' => $this->activity->risk_score,
            'location' => $this->activity->activity_data['location'] ?? 'Unknown',
            'time' => now()->toDateTimeString()
        ];
    }
}
