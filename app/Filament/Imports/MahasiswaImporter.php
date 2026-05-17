<?php

namespace App\Filament\Imports;

use App\Enums\AkademikStatus;
use App\Models\Mahasiswa;
use App\Models\User;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;

class MahasiswaImporter extends Importer
{
    protected static ?string $model = Mahasiswa::class;

    // =========================================================================
    // COLUMN DEFINITIONS
    // =========================================================================

    public static function getColumns(): array
    {
        return [
            // ─── Akun Pengguna ────────────────────────────────────────────────
            ImportColumn::make('email')
                ->label('Email Mahasiswa')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255'])
                ->example('mahasiswa@example.com')
                ->helperText('Email untuk akun login. Harus unik. Password default = NIM.')
                ->fillRecordUsing(fn () => null), // email tidak ada di tabel mahasiswa

            // ─── Data Akademik (Wajib) ────────────────────────────────────────
            ImportColumn::make('nim')
                ->label('NIM')
                ->requiredMapping()
                ->rules(['required', 'max:20', 'regex:/^\d+$/'])
                ->example('2024001001')
                ->helperText('Nomor Induk Mahasiswa (hanya angka). Digunakan juga sebagai password default.'),

            ImportColumn::make('nama_lengkap')
                ->label('Nama Lengkap')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Budi Santoso')
                ->guess(['nama', 'name', 'nama lengkap']),

            ImportColumn::make('program_studi')
                ->label('Program Studi')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Teknik Informatika')
                ->guess(['prodi', 'program studi', 'jurusan']),

            ImportColumn::make('angkatan')
                ->label('Angkatan')
                ->requiredMapping()
                ->rules(['required', 'digits:4', 'integer', 'min:2000'])
                ->integer()
                ->example('2024'),

            ImportColumn::make('semester')
                ->label('Semester')
                ->requiredMapping()
                ->rules(['required', 'integer', 'min:1', 'max:14'])
                ->integer()
                ->example('1'),

            // ─── Data Akademik (Opsional) ─────────────────────────────────────
            ImportColumn::make('fakultas')
                ->label('Fakultas')
                ->rules(['nullable', 'max:255'])
                ->example('Fakultas Ilmu Komputer')
                ->guess(['fakultas', 'faculty'])
                ->ignoreBlankState(),

            ImportColumn::make('ipk')
                ->label('IPK')
                ->rules(['nullable', 'numeric', 'min:0', 'max:4'])
                ->numeric(decimalPlaces: 2)
                ->example('3.50')
                ->ignoreBlankState(),

            ImportColumn::make('sks_lulus')
                ->label('SKS Lulus')
                ->rules(['nullable', 'integer', 'min:0'])
                ->integer()
                ->example('72')
                ->guess(['sks lulus', 'sks_lulus'])
                ->ignoreBlankState(),

            ImportColumn::make('sks_total')
                ->label('Total SKS')
                ->rules(['nullable', 'integer', 'min:0'])
                ->integer()
                ->example('144')
                ->guess(['sks total', 'sks_total', 'total sks'])
                ->ignoreBlankState(),

            ImportColumn::make('status_akademik')
                ->label('Status Akademik')
                ->rules(['nullable', 'in:aktif,cuti,lulus,drop_out,mengulang'])
                ->example('aktif')
                ->helperText('Nilai: aktif | cuti | lulus | drop_out | mengulang')
                ->guess(['status', 'status akademik'])
                ->ignoreBlankState(),

            // ─── Data Pribadi (Opsional) ──────────────────────────────────────
            ImportColumn::make('no_telepon')
                ->label('Nomor Telepon')
                ->rules(['nullable', 'max:20'])
                ->example('081234567890')
                ->guess(['telepon', 'phone', 'no telepon', 'no_telepon'])
                ->ignoreBlankState(),

            ImportColumn::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->rules(['nullable', 'date'])
                ->example('2002-05-17')
                ->helperText('Format: YYYY-MM-DD')
                ->guess(['tanggal lahir', 'tgl lahir', 'birthdate'])
                ->ignoreBlankState(),

            ImportColumn::make('alamat')
                ->label('Alamat')
                ->rules(['nullable'])
                ->example('Jl. Kebon Jeruk No. 1, Jakarta')
                ->ignoreBlankState(),
        ];
    }

    // =========================================================================
    // RECORD RESOLUTION — skip if NIM already exists
    // =========================================================================

    public function resolveRecord(): ?Mahasiswa
    {
        // Cek apakah NIM sudah terdaftar (hanya record aktif, bukan yang sudah dihapus)
        $nimExists = Mahasiswa::where('nim', $this->data['nim'])->exists();

        if ($nimExists) {
            throw new RowImportFailedException(
                "NIM [{$this->data['nim']}] sudah terdaftar. Baris dilewati."
            );
        }

        // Cek apakah email sudah digunakan di tabel users (aktif)
        $emailExists = User::where('email', $this->data['email'])->exists();

        if ($emailExists) {
            throw new RowImportFailedException(
                "Email [{$this->data['email']}] sudah terdaftar. Baris dilewati."
            );
        }

        return new Mahasiswa;
    }

    // =========================================================================
    // LIFECYCLE HOOK — buat akun User + assign role sebelum record disimpan
    // =========================================================================

    protected function beforeCreate(): void
    {
        // Buat akun User baru dengan password default dari NIM
        $user = User::create([
            'name' => $this->data['nama_lengkap'],
            'email' => $this->data['email'],
            'password' => Hash::make($this->data['nim']),
        ]);

        // Assign role 'mahasiswa' via Spatie Permission
        $user->assignRole('mahasiswa');

        // Inject user_id ke record Mahasiswa sebelum disimpan
        $this->record->user_id = $user->id;

        // Set default status_akademik jika tidak diisi
        if (blank($this->record->status_akademik)) {
            $this->record->status_akademik = AkademikStatus::AKTIF;
        }
    }

    // =========================================================================
    // CUSTOM NOTIFICATION — pesan selesai import (Bahasa Indonesia)
    // =========================================================================

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Import Data Mahasiswa Selesai';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $successful = $import->successful_rows;
        $failed = $import->getFailedRowsCount();

        $body = "{$successful} data mahasiswa berhasil diimport.";

        if ($failed > 0) {
            $body .= " {$failed} baris dilewati (duplikat NIM/email atau data tidak valid).";
        }

        return $body;
    }
}
