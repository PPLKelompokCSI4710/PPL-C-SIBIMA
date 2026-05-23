<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus tabel dosen_mahasiswa yang sudah yatim (orphaned).
     *
     * Tabel dosen sudah di-drop oleh migration simplify_dosen_management,
     * namun tabel pivot dosen_mahasiswa tidak ikut di-drop. Tabel ini masih
     * menyimpan FK dosen_id → dosen ON DELETE CASCADE, sehingga setiap kali
     * User di-delete, SQLite mencoba menelusuri cascade ke tabel dosen yang
     * sudah tidak ada, dan menghasilkan error "no such table: main.dosen".
     */
    public function up(): void
    {
        // This migration has been disabled because dosen_mahasiswa is needed.
    }

    public function down(): void
    {
        // Tidak di-restore karena tabel dosen-nya sendiri sudah tidak ada.
        // Jika perlu rollback lengkap, gunakan migration create_dosen_table asli.
    }
};
