<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * SQLite doesn't support DROP CONSTRAINT, so we recreate the table
     * without the CHECK constraint on tipe_kegiatan.
     */
    public function up(): void
    {
        // Backup existing data
        $rows = DB::table('kalender_akademiks')->get();

        // Drop and recreate table without the CHECK constraint
        DB::statement('DROP TABLE IF EXISTS "kalender_akademiks_backup"');
        DB::statement('ALTER TABLE "kalender_akademiks" RENAME TO "kalender_akademiks_backup"');

        DB::statement('
            CREATE TABLE "kalender_akademiks" (
                "id" integer primary key autoincrement not null,
                "nama_kegiatan" varchar not null,
                "tanggal_mulai" date not null,
                "jam_mulai" time,
                "tanggal_selesai" date not null,
                "deskripsi" text,
                "tipe_kegiatan" varchar default null,
                "status" varchar not null default \'Active\',
                "created_at" datetime,
                "updated_at" datetime
            )
        ');

        // Restore data
        foreach ($rows as $row) {
            DB::table('kalender_akademiks')->insert([
                'id' => $row->id,
                'nama_kegiatan' => $row->nama_kegiatan,
                'tanggal_mulai' => $row->tanggal_mulai,
                'jam_mulai' => $row->jam_mulai,
                'tanggal_selesai' => $row->tanggal_selesai,
                'deskripsi' => $row->deskripsi,
                'tipe_kegiatan' => $row->tipe_kegiatan,
                'status' => $row->status,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        DB::statement('DROP TABLE "kalender_akademiks_backup"');
    }

    public function down(): void
    {
        // Cannot easily restore CHECK constraint, no-op
    }
};
