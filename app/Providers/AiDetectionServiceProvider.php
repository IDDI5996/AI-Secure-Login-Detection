<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AiDetectionEngine;
use App\Services\VerificationService;

class AiDetectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(AiDetectionEngine::class, function ($app) {
            return new AiDetectionEngine();
        });
        
        $this->app->singleton(VerificationService::class, function ($app) {
            return new VerificationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/ai-detection.php' => config_path('ai-detection.php'),
        ], 'ai-detection-config');
        
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\TrainAiModel::class,
                \App\Console\Commands\CleanupOldLogs::class,
            ]);
        }
    }
}
