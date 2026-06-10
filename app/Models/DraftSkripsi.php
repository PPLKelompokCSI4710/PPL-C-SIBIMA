<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftSkripsi extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'judul',
        'bab',
        'file_path',
        'catatan',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
