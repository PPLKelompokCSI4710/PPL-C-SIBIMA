<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nidn')->unique();
            $table->string('nama_lengkap');
            $table->string('program_studi');
            $table->string('fakultas')->nullable();
            $table->string('jabatan_fungsional')->nullable();
            $table->string('gelar')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('kuota_mahasiswa')->default(10);
            $table->text('bio')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
