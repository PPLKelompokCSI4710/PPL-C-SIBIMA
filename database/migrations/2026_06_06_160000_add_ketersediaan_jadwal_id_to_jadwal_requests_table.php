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
        Schema::table('jadwal_requests', function (Blueprint $table) {
            $table->foreignId('ketersediaan_jadwal_id')
                ->nullable()
                ->after('dosen_id')
                ->constrained('ketersediaan_jadwals')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_requests', function (Blueprint $table) {
            $table->dropForeign(['ketersediaan_jadwal_id']);
            $table->dropColumn('ketersediaan_jadwal_id');
        });
    }
};
