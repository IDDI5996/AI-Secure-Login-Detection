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
        Schema::table('verification_attempts', function (Blueprint $table) {
            // Add indexes if they don't exist
            if (!Schema::hasIndex('verification_attempts', 'verification_attempts_login_attempt_id_index')) {
                $table->index('login_attempt_id');
            }
            
            if (!Schema::hasIndex('verification_attempts', 'verification_attempts_verification_method_index')) {
                $table->index('verification_method');
            }
            
            if (!Schema::hasIndex('verification_attempts', 'verification_attempts_verified_at_index')) {
                $table->index('verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_attempts', function (Blueprint $table) {
            $table->dropIndex(['login_attempt_id']);
            $table->dropIndex(['verification_method']);
            $table->dropIndex(['verified_at']);
        });
    }
};
