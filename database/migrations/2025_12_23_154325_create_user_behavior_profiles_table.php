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
        Schema::create('user_behavior_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('usual_locations')->nullable(); // {country, city, ip_range}
            $table->json('usual_times')->nullable(); // {hour_ranges: [], days: []}
            $table->json('device_fingerprints')->nullable();
            $table->json('typing_pattern')->nullable(); // avg_keystroke_speed, common_errors
            $table->json('mouse_movement_patterns')->nullable();
            $table->integer('login_count')->default(0);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_behavior_profiles');
    }
};
