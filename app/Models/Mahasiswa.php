<?php

namespace App\Models;

use App\Enums\AkademikStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mahasiswa extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'mahasiswa';

    /**
     * Guard all attributes.
     */
    protected $guarded = [];

    protected $fillable = [
        'user_id', 'dosen_id', 'nim', 'nama_lengkap', 'program_studi', 'fakultas',
        'angkatan', 'semester', 'ipk', 'sks_lulus', 'sks_total',
        'status_akademik', 'no_telepon', 'foto', 'tanggal_lahir',
        'alamat', 'status_kelulusan_bimbingan', 'progress_reminder_frequency_days',
        'progress_reminder_frequency', 'progress_reminder_enabled', 'last_progress_reminder_sent_at',
        'consecutive_progress_reminders',
    ];

    protected $casts = [
        'status_akademik' => AkademikStatus::class,
        'tanggal_lahir' => 'date',
        'ipk' => 'decimal:2',
        'status_kelulusan_bimbingan' => 'boolean',
        'last_progress_reminder_sent_at' => 'datetime',
        'progress_reminder_enabled' => 'boolean',
    ];

    // Relationships
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

    public function bimbingans(): HasMany
    {
        return $this->hasMany(Bimbingan::class);
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'dosen_mahasiswa')
            ->withPivot(['tanggal_penugasan', 'tanggal_berakhir', 'is_active', 'catatan']);
    }

    protected static function booted(): void
    {
        static::saved(function (Mahasiswa $mahasiswa) {
            // Sync the dosen_mahasiswa pivot table whenever dosen_id changes,
            // so the BelongsToMany `dosens` relationship stays consistent
            // with the BelongsTo `dosen` relationship.
            if ($mahasiswa->wasChanged('dosen_id')) {
                if ($mahasiswa->dosen_id) {
                    $mahasiswa->dosens()->sync([
                        $mahasiswa->dosen_id => [
                            'tanggal_penugasan' => now()->toDateString(),
                            'is_active' => true,
                        ],
                    ]);
                } else {
                    // dosen_id was set to null — detach all dosen from pivot
                    $mahasiswa->dosens()->detach();
                }
            }
        });

        static::deleting(function (Mahasiswa $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->delete();
            }
        });
    }
}
