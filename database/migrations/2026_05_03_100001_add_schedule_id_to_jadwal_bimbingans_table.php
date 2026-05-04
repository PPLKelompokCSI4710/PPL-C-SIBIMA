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
        Schema::table('jadwal_bimbingans', function (Blueprint $table) {
            $table->unsignedBigInteger('schedule_id')->nullable()->after('mahasiswa_id');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');

            // waktu bisa kita ubah menjadi nullable karena sudah ada di schedules
            $table->time('waktu')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_bimbingans', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn('schedule_id');
            $table->time('waktu')->nullable(false)->change();
        });
    }
};
