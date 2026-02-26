<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Admin fields - only add if they don't exist
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('password');
            }
            
            if (!Schema::hasColumn('users', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('is_admin');
            }
            
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('is_super_admin');
            }
            
            // Security fields - two_factor_enabled and security_questions only
            // two_factor_secret already exists from Fortify
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('role');
            }
            
            // Don't add two_factor_secret - it already exists from Fortify
            
            if (!Schema::hasColumn('users', 'security_questions')) {
                $table->json('security_questions')->nullable()->after('two_factor_secret');
            }
            
            // Add other missing fields that you might need
            if (!Schema::hasColumn('users', 'trusted_devices')) {
                $table->json('trusted_devices')->nullable()->after('security_questions');
            }
            
            if (!Schema::hasColumn('users', 'login_notifications')) {
                $table->boolean('login_notifications')->default(true)->after('trusted_devices');
            }
        });
        
        // SQLite-friendly way to check and add indexes
        $this->addIndexes();
    }
    
    /**
     * Add indexes if they don't exist (SQLite compatible)
     */
    private function addIndexes(): void
    {
        // For SQLite, we need to check indexes differently
        $indexes = DB::select("SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = 'users'");
        $indexNames = array_column($indexes, 'name');
        
        // Check and add composite index for admin fields
        if (!in_array('users_is_admin_is_super_admin_index', $indexNames)) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['is_admin', 'is_super_admin']);
            });
        }
        
        // Check and add index for role
        if (!in_array('users_role_index', $indexNames)) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop columns that we added in this migration
            $columnsToDrop = [];
            
            if (Schema::hasColumn('users', 'is_admin')) {
                $columnsToDrop[] = 'is_admin';
            }
            
            if (Schema::hasColumn('users', 'is_super_admin')) {
                $columnsToDrop[] = 'is_super_admin';
            }
            
            if (Schema::hasColumn('users', 'role')) {
                $columnsToDrop[] = 'role';
            }
            
            if (Schema::hasColumn('users', 'two_factor_enabled')) {
                $columnsToDrop[] = 'two_factor_enabled';
            }
            
            if (Schema::hasColumn('users', 'security_questions')) {
                $columnsToDrop[] = 'security_questions';
            }
            
            if (Schema::hasColumn('users', 'trusted_devices')) {
                $columnsToDrop[] = 'trusted_devices';
            }
            
            if (Schema::hasColumn('users', 'login_notifications')) {
                $columnsToDrop[] = 'login_notifications';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
        
        // Note: Don't drop indexes in down() for SQLite compatibility
        // as dropping columns will automatically remove indexes on those columns
    }
};