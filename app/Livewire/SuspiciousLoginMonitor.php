<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use Livewire\WithPagination;

class SuspiciousLoginMonitor extends Component
{
    use WithPagination;
    
    public $realtimeData = [];
    public $riskThreshold = 70; // Default 70% risk threshold
    
    protected $listeners = ['newSuspiciousActivity' => 'refreshData'];

    public function mount()
    {
        $this->loadRealtimeData();
    }

    public function loadRealtimeData()
    {
        // Load recent suspicious activities
        $this->realtimeData = SuspiciousActivity::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'user' => $activity->user->name ?? 'Unknown',
                    'type' => $activity->activity_type,
                    'risk_score' => $activity->risk_score * 100,
                    'reasons' => $activity->detection_reasons,
                    'time' => $activity->created_at->diffForHumans(),
                    'location' => $activity->activity_data['location'] ?? 'Unknown'
                ];
            })
            ->toArray();
    }

    public function updateRiskThreshold($threshold)
    {
        $this->riskThreshold = $threshold;
        $this->loadRealtimeData();
    }

    public function markAsReviewed($activityId)
    {
        $activity = SuspiciousActivity::find($activityId);
        if ($activity) {
            $activity->update([
                'status' => 'reviewed',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => 'Activity marked as reviewed!'
            ]);
            
            $this->loadRealtimeData();
        }
    }

    public function refreshData()
    {
        $this->loadRealtimeData();
    }

    public function render()
    {
        return view('livewire.suspicious-login-monitor');
    }
}