<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add only the missing security_questions field
            if (!Schema::hasColumn('users', 'security_questions')) {
                $table->json('security_questions')->nullable()->after('two_factor_secret');
            }
            
            // Add trusted_devices if missing
            if (!Schema::hasColumn('users', 'trusted_devices')) {
                $table->json('trusted_devices')->nullable()->after('security_questions');
            }
            
            // Add login_notifications if missing
            if (!Schema::hasColumn('users', 'login_notifications')) {
                $table->boolean('login_notifications')->default(true)->after('trusted_devices');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['security_questions', 'trusted_devices', 'login_notifications'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};