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
        Schema::create('threat_database', function (Blueprint $table) {
            $table->id();
            $table->string('threat_type'); // ip, pattern, behavior, malware
            $table->json('threat_data');
            $table->string('action'); // add, remove, update
            $table->foreignId('added_by')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threat_database');
    }
};
