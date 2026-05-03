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
        Schema::table('kalender_akademiks', function (Blueprint $table) {
            if (! Schema::hasColumn('kalender_akademiks', 'tipe_kegiatan')) {
                $table->string('tipe_kegiatan')->nullable();
            }
            if (! Schema::hasColumn('kalender_akademiks', 'status')) {
                $table->string('status')->default('Active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kalender_akademiks', function (Blueprint $table) {
            $table->dropColumn(['tipe_kegiatan', 'status']);
        });
    }
};
