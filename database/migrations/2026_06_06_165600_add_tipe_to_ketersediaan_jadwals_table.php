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
        Schema::table('ketersediaan_jadwals', function (Blueprint $table) {
            $table->enum('tipe', ['online', 'offline'])->default('offline')->after('kuota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ketersediaan_jadwals', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
