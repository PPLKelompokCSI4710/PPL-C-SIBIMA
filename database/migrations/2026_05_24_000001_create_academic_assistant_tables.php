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
        Schema::create('academic_assistant_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->default('Sesi Baru');
            $table->timestamps();
        });

        Schema::create('academic_assistant_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('academic_assistant_sessions')->cascadeOnDelete();
            $table->string('role'); // 'user' or 'model'
            $table->longText('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_assistant_messages');
        Schema::dropIfExists('academic_assistant_sessions');
    }
};
