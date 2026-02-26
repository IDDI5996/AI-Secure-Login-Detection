<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use App\Services\AiModelTrainer;
/**
 * Description of TrainAiModel
 *
 * @author IDRISAH
 */
class TrainAiModel extends Command {
    protected $signature = 'ai:train';
    protected $description = 'Train the AI detection model';
    
    public function handle()
    {
        $this->info('Starting AI model training...');
        
        $trainer = new AiModelTrainer();
        $result = $trainer->train();
        
        if ($result['success']) {
            $this->info('AI model trained successfully!');
            $this->info('Accuracy: ' . $result['accuracy']);
            $this->info('False positive rate: ' . $result['false_positive_rate']);
        } else {
            $this->error('AI model training failed: ' . $result['error']);
        }
    }
}
