<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'catatan_konsultasis';

    protected $fillable = [
        'jadwal_bimbingan_id',
        'dosen_id',
        'mahasiswa_id',
        'tanggal',
        'topik',
        'catatan',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function jadwalBimbingan()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'jadwal_bimbingan_id');
    }
}
