<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->integer('progress_reminder_frequency_days')->default(14)->after('status_akademik');
            $table->boolean('progress_reminder_enabled')->default(true)->after('progress_reminder_frequency_days');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['progress_reminder_frequency_days', 'progress_reminder_enabled']);
        });
    }
};
