<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catatan_konsultasis', function (Blueprint $table) {
            $table->foreignId('jadwal_bimbingan_id')
                ->nullable()
                ->after('id')
                ->constrained('jadwal_bimbingans')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('catatan_konsultasis', function (Blueprint $table) {
            $table->dropForeign(['jadwal_bimbingan_id']);
            $table->dropColumn('jadwal_bimbingan_id');
        });
    }
};
