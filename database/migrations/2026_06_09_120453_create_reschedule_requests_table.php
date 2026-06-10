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
        Schema::create('reschedule_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_bimbingan_id')->constrained('jadwal_bimbingans')->cascadeOnDelete();
            $table->foreignId('ketersediaan_jadwal_lama_id')->nullable()->constrained('ketersediaan_jadwals')->nullOnDelete();
            $table->foreignId('ketersediaan_jadwal_baru_id')->constrained('ketersediaan_jadwals')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('alasan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reschedule_requests');
    }
};
