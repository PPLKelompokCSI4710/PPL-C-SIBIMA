<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_bimbingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ketersediaan_jadwal_id')->nullable()->constrained('ketersediaan_jadwals')->nullOnDelete();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->string('judul_ta')->nullable();
            $table->text('topik_bimbingan')->nullable();
            $table->enum('tipe', ['online', 'offline'])->nullable();
            $table->enum('status', ['pending', 'rejected', 'approved', 'completed', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_bimbingans');
    }
};
