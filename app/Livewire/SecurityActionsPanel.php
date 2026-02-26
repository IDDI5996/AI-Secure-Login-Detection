<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\BlockedIp;

class SecurityActionsPanel extends Component
{
    public $searchTerm = '';
    public $users = [];
    public $blockedIps = [];
    public $systemLocked = false;
    
    // Form data
    public $lockSystem = [
        'reason' => '',
        'duration_minutes' => 60
    ];
    
    public $blockIp = [
        'ip_address' => '',
        'reason' => '',
        'duration_hours' => 24
    ];
    
    public $lockUser = [
        'user_id' => '',
        'reason' => '',
        'duration_hours' => 24
    ];
    
    public $require2fa = [
        'user_id' => '',
        'reason' => ''
    ];
    
    public $generateReport = [
        'report_type' => 'security',
        'start_date' => '',
        'end_date' => '',
        'format' => 'pdf'
    ];
    
    public $updateThreatDb = [
        'threat_type' => 'ip',
        'action' => 'add',
        'data' => ''
    ];
    
    protected $rules = [
        'lockSystem.reason' => 'required|min:10',
        'lockSystem.duration_minutes' => 'required|integer|min:1|max:1440',
        
        'blockIp.ip_address' => 'required|ip',
        'blockIp.reason' => 'required|min:10',
        'blockIp.duration_hours' => 'required|integer|min:1|max:720',
        
        'lockUser.user_id' => 'required|exists:users,id',
        'lockUser.reason' => 'required|min:10',
        'lockUser.duration_hours' => 'required|integer|min:1|max:720',
        
        'require2fa.user_id' => 'required|exists:users,id',
        'require2fa.reason' => 'required|min:10',
        
        'generateReport.report_type' => 'required|in:security,threats,users,comprehensive',
        'generateReport.start_date' => 'required|date',
        'generateReport.end_date' => 'required|date|after_or_equal:generateReport.start_date',
        'generateReport.format' => 'required|in:pdf,csv,json',
        
        'updateThreatDb.threat_type' => 'required|in:ip,pattern,behavior,malware',
        'updateThreatDb.action' => 'required|in:add,remove,update',
        'updateThreatDb.data' => 'required'
    ];
    
    public function mount()
    {
        $this->loadData();
        $this->generateReport['start_date'] = now()->subDays(7)->format('Y-m-d');
        $this->generateReport['end_date'] = now()->format('Y-m-d');
    }
    
    public function loadData()
    {
        // Load users for dropdowns
        $this->users = User::where('name', 'like', "%{$this->searchTerm}%")
            ->orWhere('email', 'like', "%{$this->searchTerm}%")
            ->limit(20)
            ->get(['id', 'name', 'email', 'is_locked']);
            
        // Load blocked IPs
        $this->blockedIps = BlockedIp::with('blocker')
            ->where('is_active', true)
            ->orderBy('blocked_at', 'desc')
            ->limit(10)
            ->get();
    }
    
    public function updatedSearchTerm()
    {
        $this->loadData();
    }
    
    public function lockSystem()
    {
        $this->validateOnly('lockSystem');
        
        // Call API endpoint
        $this->dispatch('perform-action', 
            action: 'lockSystem',
            data: $this->lockSystem,
            message: 'Locking system...'
        );
    }
    
    public function unlockSystem()
    {
        $this->dispatch('perform-action', 
            action: 'unlockSystem',
            data: [],
            message: 'Unlocking system...'
        );
    }
    
    public function blockIpAddress()
    {
        $this->validateOnly('blockIp');
        
        $this->dispatch('perform-action', 
            action: 'blockIp',
            data: $this->blockIp,
            message: 'Blocking IP address...'
        );
    }
    
    // Add similar methods for other actions...
    
    public function render()
    {
        return view('livewire.security-actions-panel');
    }
}
