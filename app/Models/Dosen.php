<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $guarded = [];
}
=======
use Illuminate\Database\Eloquent\SoftDeletes;

class Dosen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'nidn',
        'nama_lengkap',
        'program_studi',
        'fakultas',
        'jabatan_akademik',
        'jabatan_fungsional',
        'gelar',
        'keahlian',
        'no_telepon',
        'foto',
        'is_active',
        'kuota_mahasiswa',
        'bio',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bimbingans()
    {
        return $this->hasMany(Bimbingan::class);
    }

    public function mahasiswas()
    {
        return $this->belongsToMany(Mahasiswa::class, 'dosen_mahasiswa')
            ->withPivot(['tanggal_penugasan', 'tanggal_berakhir', 'is_active', 'catatan']);
    }
}
>>>>>>> main
