<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SuspiciousActivity;
use App\Models\LoginAttempt;
use Carbon\Carbon;

class IncidentTimeline extends Component
{
    public $incidents = [];
    public $loading = true;
    
    public function mount()
    {
        $this->loadIncidents();
    }
    
    public function loadIncidents()
    {
        // Get incidents from last 24 hours
        $twentyFourHoursAgo = Carbon::now()->subHours(24);
        
        $this->incidents = SuspiciousActivity::with(['user'])
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($incident) {
                return [
                    'id' => $incident->id,
                    'user' => $incident->user->name ?? 'Unknown User',
                    'email' => $incident->user->email ?? 'N/A',
                    'type' => $this->formatActivityType($incident->activity_type),
                    'description' => $incident->description ?? 'Security incident detected',
                    'time' => $incident->created_at->format('h:i A'),
                    'date' => $incident->created_at->format('M d'),
                    'timestamp' => $incident->created_at,
                    'risk_score' => $incident->risk_score,
                    'risk_percentage' => round($incident->risk_score * 100),
                    'status' => $incident->status,
                    'risk_color' => $this->getRiskColor($incident->risk_score),
                    'status_color' => $this->getStatusColor($incident->status),
                    'icon' => $this->getIncidentIcon($incident->activity_type),
                    'detection_reasons' => $incident->detection_reasons ?? []
                ];
            })
            ->toArray();
            
        $this->loading = false;
    }
    
    private function formatActivityType($type)
    {
        $types = [
            'failed_login' => 'Failed Login',
            'unusual_location' => 'Unusual Location',
            'new_device' => 'New Device',
            'multiple_failures' => 'Multiple Failures',
            'suspicious_pattern' => 'Suspicious Pattern',
            'high_risk' => 'High Risk Login',
            'password_attempt' => 'Password Attempt'
        ];
        
        return $types[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
    
    private function getRiskColor($score)
    {
        if ($score >= 0.8) return 'red';
        if ($score >= 0.6) return 'orange';
        if ($score >= 0.4) return 'yellow';
        return 'green';
    }
    
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'resolved' => 'green',
            'false_positive' => 'gray'
        ];
        
        return $colors[$status] ?? 'gray';
    }
    
    private function getIncidentIcon($type)
    {
        $icons = [
            'failed_login' => '🔐',
            'unusual_location' => '🌍',
            'new_device' => '💻',
            'multiple_failures' => '⚠️',
            'suspicious_pattern' => '🕵️',
            'high_risk' => '🚨',
            'password_attempt' => '🔑'
        ];
        
        return $icons[$type] ?? '📝';
    }
    
    public function markAsReviewed($incidentId)
    {
        $incident = SuspiciousActivity::find($incidentId);
        
        if ($incident) {
            $incident->update([
                'status' => 'reviewed',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);
            
            $this->loadIncidents(); // Refresh the list
            $this->dispatch('notify', 
                message: 'Incident marked as reviewed.',
                type: 'success'
            );
        }
    }
    
    public function markAsFalsePositive($incidentId)
    {
        $incident = SuspiciousActivity::find($incidentId);
        
        if ($incident) {
            $incident->update([
                'status' => 'false_positive',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => 'Marked as false positive by security analyst'
            ]);
            
            $this->loadIncidents(); // Refresh the list
            $this->dispatch('notify', 
                message: 'Incident marked as false positive.',
                type: 'info'
            );
        }
    }
    
    public function render()
    {
        return view('livewire.incident-timeline');
    }
}
