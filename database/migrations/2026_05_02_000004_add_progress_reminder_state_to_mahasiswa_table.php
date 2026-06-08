<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->enum('progress_reminder_frequency', ['weekly', 'biweekly'])
                ->default('biweekly')
                ->after('progress_reminder_frequency_days');
            $table->dateTime('last_progress_reminder_sent_at')
                ->nullable()
                ->after('progress_reminder_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['progress_reminder_frequency', 'last_progress_reminder_sent_at']);
        });
    }
};
