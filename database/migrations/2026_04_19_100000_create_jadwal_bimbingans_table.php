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
            // Nanti bisa disesuaikan dengan tipe relasinya (foreign key ke users/mahasiswa/dosen)
            $table->unsignedBigInteger('dosen_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->date('tanggal');
            $table->time('waktu');
            $table->text('topik_bimbingan');
            $table->enum('tipe', ['online', 'offline']);
            $table->enum('status', ['pending', 'rejected', 'approved', 'completed', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_bimbingans');
    }
};
