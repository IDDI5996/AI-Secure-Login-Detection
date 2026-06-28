<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trusted_contexts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_fingerprint', 64)->comment('SHA256 of user agent + accept headers + IP range');
            $table->string('ip_range', 15)->comment('first three octets, e.g. 192.168.1');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('device_type')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'device_fingerprint', 'ip_range', 'country']);
            $table->index(['user_id', 'last_used_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trusted_contexts');
    }
};