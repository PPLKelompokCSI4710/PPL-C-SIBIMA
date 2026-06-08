<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'google_access_token')) {
                $table->text('google_access_token')->nullable();
                $table->text('google_refresh_token')->nullable();
                $table->string('google_calendar_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_access_token', 'google_refresh_token', 'google_calendar_id']);
        });
    }
};
