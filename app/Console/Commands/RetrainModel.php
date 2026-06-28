<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class RetrainModel extends Command
{
    protected $signature = 'ai:retrain
                            {--days=90 : Days of data to include}
                            {--n-estimators=8 : Number of trees for Isolation Forest}';

    protected $description = 'Export training data, retrain the model, and deploy it';

    public function handle()
    {
        $this->info('🔄 Starting AI model retraining...');

        // 1. Export data
        $this->call('ai:export-training', [
            '--days' => $this->option('days'),
            '--output' => 'training_data.csv',
        ]);

        $csvPath = storage_path('app/ai/models/training_data.csv');
        if (!file_exists($csvPath)) {
            $this->error('Training data export failed.');
            return 1;
        }

        // 2. Run Python training
        $this->info('🐍 Training Isolation Forest model...');
        $trainScript = storage_path('app/ai/models/train.py');
        if (!file_exists($trainScript)) {
            $this->error('train.py not found at ' . $trainScript);
            return 1;
        }

        $outputDir = storage_path('app/ai/models/detector_package_new');

        $command = [
            'python3',
            $trainScript,
            $csvPath,
            '--output',
            $outputDir,
            '--n-estimators',
            $this->option('n-estimators'),
        ];

        $process = new Process($command);
        $process->setTimeout(600);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            $this->error('Training failed: ' . $process->getErrorOutput());
            return 1;
        }

        // 3. Deploy new model
        $this->info('📦 Deploying new model...');
        $targetDir = storage_path('app/ai/models');
        $newFiles = glob($outputDir . '/*');
        foreach ($newFiles as $file) {
            $basename = basename($file);
            copy($file, $targetDir . '/' . $basename);
        }

        // Clean up
        $this->deleteDirectory($outputDir);

        // 4. Clear config cache
        $this->call('config:clear');

        $this->info('✅ Model retrained and deployed successfully!');
        return 0;
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}