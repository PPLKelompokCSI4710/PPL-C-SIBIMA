<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * SQLite doesn't support DROP CONSTRAINT, so we recreate the table
     * without the CHECK constraint on tipe_kegiatan.
     */
    public function up(): void
    {
        // This SQLite-specific migration is disabled for MySQL
    }

    public function down(): void
    {
        // Cannot easily restore CHECK constraint, no-op
    }
};
