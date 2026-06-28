<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportTrainingData extends Command
{
    protected $signature = 'ai:export-training
                            {--days=90 : Number of days of data to export}
                            {--output=training_data.csv : Output file name}';

    protected $description = 'Export login data for AI model retraining';

    public function handle()
    {
        $days = $this->option('days');
        $output = $this->option('output');
        $path = storage_path("app/ai/models/{$output}");

        $this->info("Exporting login data from the last {$days} days...");

        $attempts = LoginAttempt::with('user')
            ->where('attempted_at', '>=', Carbon::now()->subDays($days))
            ->get();

        if ($attempts->isEmpty()) {
            $this->warn('No login attempts found in the specified period.');
            return 0;
        }

        $this->info("Found {$attempts->count()} login attempts.");

        $extractor = app(\App\Services\AiFeatureExtractor::class);

        $csv = fopen($path, 'w');
        // Headers
        fputcsv($csv, [
            'status', 'hour', 'day_of_week', 'minute', 'is_weekend',
            'login_count_24h', 'failed_count_24h', 'fail_rate_24h',
            'unique_ips_24h', 'unique_countries_24h', 'unique_devices_24h',
            'ip_freq', 'country_freq', 'device_type_freq', 'browser_freq'
        ]);

        foreach ($attempts as $attempt) {
            $user = $attempt->user;
            if (!$user) continue;

            // Build fake request with IP and user agent
            $request = new \Illuminate\Http\Request();
            $request->server->set('REMOTE_ADDR', $attempt->ip_address);
            $request->headers->set('User-Agent', $attempt->user_agent);

            try {
                $features = $extractor->extract($user, $request, $attempt->attempted_at);
                fputcsv($csv, array_values($features));
            } catch (\Exception $e) {
                $this->error("Failed to extract features for attempt {$attempt->id}: " . $e->getMessage());
            }
        }

        fclose($csv);

        $this->info("Training data exported to {$path}");
        return 0;
    }
}