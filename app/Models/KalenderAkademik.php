<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model
{
    protected $fillable = [
        'nama_kegiatan',
        'tanggal_mulai',
        'jam_mulai',
        'tanggal_selesai',
        'deskripsi',
    ];
}
