<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('device_type')->nullable();
            $table->boolean('is_successful')->default(false);
            $table->boolean('is_suspicious')->default(false);
            $table->float('risk_score', 3, 2)->default(0.00); // 0.00 to 1.00
            $table->json('detection_factors')->nullable();
            $table->timestamp('attempted_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['user_id', 'attempted_at']);
            $table->index(['is_suspicious', 'attempted_at']);
            $table->index('risk_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
