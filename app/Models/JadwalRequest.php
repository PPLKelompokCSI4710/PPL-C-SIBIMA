<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dosen_id',
        'ketersediaan_jadwal_id',
        'tipe_request',
        'judul',
        'deskripsi',
        'tanggal',
        'jam',
        'status',
        'alasan_penolakan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function ketersediaanJadwal()
    {
        return $this->belongsTo(KetersediaanJadwal::class, 'ketersediaan_jadwal_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'user_id', 'user_id');
    }
}
