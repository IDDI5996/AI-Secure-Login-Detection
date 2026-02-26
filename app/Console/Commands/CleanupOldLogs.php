<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoginAttempt;
use Carbon\Carbon;
/**
 * Description of CleanupOldLogs
 *
 * @author IDRISAH
 */
class CleanupOldLogs extends Command {
   protected $signature = 'logs:cleanup {--days=90 : Days to keep logs}';
    protected $description = 'Cleanup old login attempt logs';
    
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $deleted = LoginAttempt::where('attempted_at', '<', $cutoffDate)
            ->where('is_suspicious', false)
            ->delete();
            
        $this->info("Deleted {$deleted} old login logs (older than {$days} days).");
        
        // Archive suspicious activities
        $this->archiveSuspiciousActivities($cutoffDate);
    }
}
