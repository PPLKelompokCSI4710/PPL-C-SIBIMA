<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('jadwal_requests', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_requests', function (Blueprint $table) {
            $table->dropColumn('alasan_penolakan');
        });
    }
};
