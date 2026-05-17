<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_bimbingans', function (Blueprint $table) {
            $table->foreignId('ketersediaan_jadwal_id')
                ->nullable()
                ->after('mahasiswa_id')
                ->constrained('ketersediaan_jadwals')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_bimbingans', function (Blueprint $table) {
            $table->dropForeign(['ketersediaan_jadwal_id']);
            $table->dropColumn('ketersediaan_jadwal_id');
        });
    }
};
