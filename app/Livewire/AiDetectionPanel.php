<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoginAttempt;
use App\Services\AiDetectionEngine;
use Carbon\Carbon;

class AiDetectionPanel extends Component
{
    public $riskScore = 0;
    public $detectionFactors = [];
    public $locationStatus = 'Normal';
    public $deviceStatus = 'Normal';
    public $timeStatus = 'Normal';
    public $velocityStatus = 'Normal';
    public $ipStatus = 'Normal';
    
    public $isNormal = true;
    public $lastAnalysisTime;

    protected $listeners = ['analyzeLogin' => 'analyzeSample'];

    public function mount()
    {
        $this->analyzeSample();
        $this->lastAnalysisTime = now();
    }

    public function analyzeSample()
    {
        // Get recent login attempt for analysis
        $recentAttempt = LoginAttempt::latest()->first();
        
        if ($recentAttempt) {
            $this->riskScore = $recentAttempt->risk_score * 100;
            $this->detectionFactors = $recentAttempt->detection_factors ?? [];
            
            // Update statuses based on factors
            $this->updateStatuses();
            
            $this->isNormal = $this->riskScore < 70;
            $this->lastAnalysisTime = now();
        }
    }

    private function updateStatuses()
    {
        foreach ($this->detectionFactors as $factor => $data) {
            $risk = $data['risk'] ?? 0;
            
            switch ($factor) {
                case 'location':
                    $this->locationStatus = $risk >= 0.7 ? 'High Risk' : 
                                          ($risk >= 0.4 ? 'Medium Risk' : 'Normal');
                    break;
                case 'device':
                    $this->deviceStatus = $risk >= 0.7 ? 'High Risk' : 
                                        ($risk >= 0.4 ? 'Medium Risk' : 'Normal');
                    break;
                case 'time_pattern':
                    $this->timeStatus = $risk >= 0.7 ? 'High Risk' : 
                                      ($risk >= 0.4 ? 'Medium Risk' : 'Normal');
                    break;
                case 'velocity':
                    $this->velocityStatus = $risk >= 0.7 ? 'High Risk' : 
                                          ($risk >= 0.4 ? 'Medium Risk' : 'Normal');
                    break;
                case 'ip_reputation':
                    $this->ipStatus = $risk >= 0.7 ? 'High Risk' : 
                                    ($risk >= 0.4 ? 'Medium Risk' : 'Normal');
                    break;
            }
        }
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'High Risk' => 'text-red-500',
            'Medium Risk' => 'text-yellow-500',
            default => 'text-green-500'
        };
    }

    public function getStatusIcon($status)
    {
        return match($status) {
            'High Risk' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'Medium Risk' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z',
            default => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        };
    }

    public function render()
    {
        return view('livewire.ai-detection-panel');
    }
}