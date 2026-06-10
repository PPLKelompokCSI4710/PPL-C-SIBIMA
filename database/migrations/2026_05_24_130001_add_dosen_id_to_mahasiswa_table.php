<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom dosen_id ke tabel mahasiswa.
     *
     * Kolom ini menyimpan foreign key ke tabel dosen,
     * merepresentasikan hubungan bimbingan 1-to-Many
     * (satu mahasiswa memiliki satu dosen pembimbing utama).
     */
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreignId('dosen_id')
                ->nullable()
                ->after('user_id')
                ->constrained('dosen')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
        });
    }
};
