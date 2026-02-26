<?php

namespace App\Livewire;

use Livewire\Component;

class AlertSystem extends Component
{
    public $alerts = [];
    public $alertId = 0;

    protected $listeners = [
        'alertAdded' => 'addAlert',
        'alertDismissed' => 'dismissAlert'
    ];

    public function mount()
    {
        // Load any persistent alerts from session
        if (session()->has('alerts')) {
            $this->alerts = session('alerts');
        }
    }

    public function addAlert($type, $title, $message, $duration = 5000)
    {
        $this->alertId++;
        
        $alert = [
            'id' => $this->alertId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'duration' => $duration
        ];

        $this->alerts[] = $alert;
        
        // Auto-dismiss after duration
        if ($duration > 0) {
            $this->dispatchBrowserEvent('auto-dismiss', [
                'id' => $this->alertId,
                'duration' => $duration
            ]);
        }

        // Store in session for persistence
        session()->flash('alerts', $this->alerts);
    }

    public function dismissAlert($alertId)
    {
        $this->alerts = array_filter($this->alerts, function($alert) use ($alertId) {
            return $alert['id'] != $alertId;
        });
        
        session()->flash('alerts', $this->alerts);
    }

    public function alertClasses($type)
    {
        $classes = [
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'danger' => 'bg-red-50 border-red-200 text-red-800',
            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'info' => 'bg-blue-50 border-blue-200 text-blue-800',
        ];

        return $classes[$type] ?? $classes['info'];
    }

    public function iconForType($type)
    {
        $icons = [
            'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'danger' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z',
            'info' => 'M13 16h-1v-4h1m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ];

        return $icons[$type] ?? $icons['info'];
    }

    public function render()
    {
        return view('livewire.alert-system');
    }
}