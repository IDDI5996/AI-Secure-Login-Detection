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
        Schema::create('verification_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('login_attempt_id')->nullable()->constrained('login_attempts');
            $table->string('verification_method'); // 2fa, email, security_questions, biometric
            $table->boolean('is_successful');
            $table->json('verification_data')->nullable();
            $table->timestamp('verified_at');
            $table->timestamps();
            
            $table->index(['user_id', 'verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_attempts');
    }
};
