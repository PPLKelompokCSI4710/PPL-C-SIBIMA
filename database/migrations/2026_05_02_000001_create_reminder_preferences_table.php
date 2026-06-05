<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // PBI 15 - schedule reminder stages
            $table->boolean('schedule_reminder_enabled')->default(true);
            $table->boolean('stage_h3_enabled')->default(true);
            $table->boolean('stage_h1_enabled')->default(true);
            $table->boolean('stage_h2_enabled')->default(true);

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_preferences');
    }
};
