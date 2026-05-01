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
        // 1. Tambahkan kolom ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('program_studi')->nullable()->after('password');
            $table->string('fakultas')->nullable()->after('program_studi');
            $table->integer('kuota_pembimbingan')->default(10)->after('fakultas');
        });

        // 2. Hapus tabel dosen (karena data tidak perlu dipindahkan sesuai instruksi)
        Schema::dropIfExists('dosen');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['program_studi', 'fakultas', 'kuota_pembimbingan']);
        });

        // Catatan: Down tidak mengembalikan tabel dosen karena strukturnya cukup kompleks.
        // Jika perlu rollback penuh, gunakan migration asli create_dosen_table.
    }
};
