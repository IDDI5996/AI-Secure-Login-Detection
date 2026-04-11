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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // e.g. IA 429
            $table->string('name');                    // Full course name
            $table->string('semester')->default('Semester 2');
            $table->integer('year')->default(4);       // Fourth year
            $table->decimal('credits', 3, 1)->nullable();
            $table->enum('type', ['core', 'elective'])->default('core');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
