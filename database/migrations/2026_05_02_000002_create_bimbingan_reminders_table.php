<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimbingan_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bimbingan_id')->constrained('bimbingans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('stage', ['h3', 'h1', 'h2']);
            $table->dateTime('send_at');
            $table->enum('status', ['pending', 'sent', 'canceled'])->default('pending');
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('canceled_at')->nullable();

            // Snapshot detail at scheduling-time (helps keep message consistent if minor changes happen)
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index(['status', 'send_at']);
            $table->unique(['bimbingan_id', 'user_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimbingan_reminders');
    }
};
