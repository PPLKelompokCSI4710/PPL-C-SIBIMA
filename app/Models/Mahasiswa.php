<?php

namespace App\Models;

use App\Enums\AkademikStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mahasiswa extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     * Eksplisit karena nama tabel tidak mengikuti konvensi plural Laravel.
     */
    protected $table = 'mahasiswa';

    /**
     * Menggunakan $guarded = [] agar semua kolom dapat di-mass assign.
     * Form Filament sendiri sudah bertindak sebagai whitelist implisit.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status_akademik' => AkademikStatus::class,
            'tanggal_lahir' => 'date',
            'ipk' => 'decimal:2',
            'status_kelulusan_bimbingan' => 'boolean',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Mendapatkan akun User yang terhubung dengan profil mahasiswa ini.
     * (Inverse of One-to-One)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan Dosen Pembimbing utama mahasiswa ini.
     * (Inverse of One-to-Many — satu mahasiswa memiliki satu dosen pembimbing)
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function studentProgress(): HasOne
    {
        return $this->hasOne(StudentProgress::class, 'user_id', 'user_id');
    }

    // =========================================================================
    // LIFECYCLE HOOKS
    // =========================================================================

    protected static function booted(): void
    {
        // Ketika mahasiswa dihapus (soft-delete), hapus juga akun User-nya
        // agar email tidak menghalangi import ulang di masa mendatang.
        static::deleting(function (Mahasiswa $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->delete();
            }
        });
    }
}
