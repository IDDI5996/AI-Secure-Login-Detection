<?php

namespace App\Console\Commands;

use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use Illuminate\Console\Command;

class MonitorModel extends Command
{
    protected $signature = 'ai:monitor';
    protected $description = 'Monitor AI model performance and generate report';

    public function handle()
    {
        $this->info('📊 AI Model Performance Report');
        $this->info('=============================');
        
        $total = LoginAttempt::count();
        $suspicious = LoginAttempt::where('is_suspicious', true)->count();
        $verified = SuspiciousActivity::where('status', 'resolved')->count();
        $pending = SuspiciousActivity::where('status', 'pending')->count();
        $falsePositives = SuspiciousActivity::where('status', 'false_positive')->count();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Login Attempts', $total],
                ['Suspicious Detected', $suspicious],
                ['Verified (Legitimate)', $verified],
                ['Pending Review', $pending],
                ['False Positives', $falsePositives],
                ['False Positive Rate', $total > 0 ? round(($falsePositives / $total) * 100, 2) . '%' : '0%'],
            ]
        );
    }
}