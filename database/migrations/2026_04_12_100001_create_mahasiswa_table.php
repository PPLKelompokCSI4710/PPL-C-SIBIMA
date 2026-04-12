<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nim')->unique();
            $table->string('nama_lengkap');
            $table->string('program_studi');
            $table->string('fakultas')->nullable();
            $table->string('angkatan', 4);
            $table->string('semester')->default(1);
            $table->decimal('ipk', 4, 2)->nullable();
            $table->integer('sks_lulus')->default(0);
            $table->integer('sks_total')->default(144);
            $table->enum('status_akademik', ['aktif', 'cuti', 'lulus', 'drop_out', 'mengulang'])->default('aktif');
            $table->string('no_telepon')->nullable();
            $table->string('foto')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('status_kelulusan_bimbingan')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
