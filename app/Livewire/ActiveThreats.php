<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SuspiciousActivity;
use App\Models\LoginAttempt;
use Carbon\Carbon;

class ActiveThreats extends Component
{
    public $threats = [];
    public $loading = true;
    
    public function mount()
    {
        $this->loadThreats();
    }
    
    public function loadThreats()
    {
        $today = Carbon::today();
        
        $this->threats = SuspiciousActivity::with(['user'])
            ->where('status', 'pending')
            ->where('risk_score', '>=', 0.7)
            ->orderBy('risk_score', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($threat) {
                return [
                    'id' => $threat->id,
                    'user' => $threat->user->name ?? 'Unknown',
                    'type' => $threat->activity_type,
                    'risk_score' => $threat->risk_score,
                    'risk_percentage' => round($threat->risk_score * 100),
                    'description' => $threat->description,
                    'detection_reasons' => $threat->detection_reasons,
                    'time' => $threat->created_at->diffForHumans(),
                    'is_high_risk' => $threat->risk_score >= 0.8,
                    'risk_color' => $this->getRiskColor($threat->risk_score)
                ];
            })
            ->toArray();
            
        $this->loading = false;
    }
    
    private function getRiskColor($score)
    {
        if ($score >= 0.8) return 'red';
        if ($score >= 0.6) return 'orange';
        if ($score >= 0.4) return 'yellow';
        return 'green';
    }
    
    public function markAsReviewed($threatId)
    {
        $threat = SuspiciousActivity::find($threatId);
        
        if ($threat) {
            $threat->update([
                'status' => 'reviewed',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);
            
            $this->loadThreats(); // Refresh the list
            $this->dispatch('threat-reviewed', message: 'Threat marked as reviewed.');
        }
    }
    
    public function render()
    {
        return view('livewire.active-threats');
    }
}
