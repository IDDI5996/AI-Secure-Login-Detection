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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('role');
        $table->timestamp('locked_at')->nullable();
        $table->foreignId('locked_by')->nullable()->constrained('users');
        $table->text('lock_reason')->nullable();
        $table->timestamp('unlocks_at')->nullable();
        $table->timestamp('unlocked_at')->nullable();
        $table->foreignId('unlocked_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
