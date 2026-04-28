<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoginAttempt;

class RecentActivities extends Component
{
    public $activities;

    public function mount()
    {
        $this->activities = LoginAttempt::with('user')
            ->latest('attempted_at')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.recent-activities', [
            'activities' => $this->activities,
        ]);
    }
}