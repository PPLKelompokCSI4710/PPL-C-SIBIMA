<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_konsultasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_bimbingan_id')->constrained('jadwal_bimbingans')->cascadeOnDelete();
            $table->text('catatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_konsultasis');
    }
};
