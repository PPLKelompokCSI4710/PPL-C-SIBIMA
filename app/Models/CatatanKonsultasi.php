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
        'catatan',
    ];

    public function jadwalBimbingan()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'jadwal_bimbingan_id');
    }
}
