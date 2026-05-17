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
        Schema::create('jadwal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('dosen_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('tipe_request'); // 'bimbingan' or 'umum'
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal');
            $table->time('jam');
            $table->string('status')->default('pending_dosen'); // pending_dosen, rejected_dosen, pending_admin, rejected_admin, approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_requests');
    }
};
