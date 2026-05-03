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
        Schema::create('catatan_konsultasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dosen_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->date('tanggal');
            $table->string('topik');
            $table->text('catatan');
            $table->timestamps();

            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_konsultasis');
    }
};
