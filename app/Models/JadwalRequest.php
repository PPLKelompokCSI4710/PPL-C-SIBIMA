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
}
