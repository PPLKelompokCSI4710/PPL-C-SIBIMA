<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->date('tanggal_penugasan');
            $table->date('tanggal_berakhir')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['dosen_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen_mahasiswa');
    }
};
