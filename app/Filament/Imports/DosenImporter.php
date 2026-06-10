<?php

namespace App\Filament\Imports;

use App\Enums\AkademikStatus;
use App\Models\Dosen;
use App\Models\User;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;

class DosenImporter extends Importer
{
    protected static ?string $model = Dosen::class;

    // -------------------------------------------------------------------------
    // COLUMN DEFINITIONS
    // -------------------------------------------------------------------------
    public static function getColumns(): array
    {
        return [
            // ─── Akun Pengguna ────────────────────────────────────────────────
            ImportColumn::make('email')
                ->label('Email Dosen')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255'])
                ->example('dosen@example.com')
                ->helperText('Email untuk akun login. Harus unik. Password default = KODE_DOSEN.')
                ->fillRecordUsing(fn () => null),

            // ─── Identitas Dosen ───────────────────────────────────────────────
            ImportColumn::make('kode_dosen')
                ->label('Kode Dosen')
                ->requiredMapping()
                ->rules(['required', 'max:20', 'alpha_num'])
                ->example('DSN001')
                ->helperText('Kode unik untuk dosen, digunakan juga sebagai password default.')
                ->guess(['kode', 'kode_dosen']),

            ImportColumn::make('nama_lengkap')
                ->label('Nama Lengkap')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Dr. Siti Rahma')
                ->guess(['nama', 'name', 'nama lengkap']),

            ImportColumn::make('program_studi')
                ->label('Program Studi')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Teknik Informatika')
                ->guess(['prodi', 'program studi', 'jurusan']),

            ImportColumn::make('fakultas')
                ->label('Fakultas')
                ->rules(['nullable', 'max:255'])
                ->example('Fakultas Ilmu Komputer')
                ->guess(['fakultas', 'faculty'])
                ->ignoreBlankState(),

            // ─── Opsional ───────────────────────────────────────────────────────
            ImportColumn::make('no_telepon')
                ->label('Nomor Telepon')
                ->rules(['nullable', 'max:20'])
                ->example('081234567890')
                ->guess(['telepon', 'phone', 'no telepon'])
                ->ignoreBlankState(),
        ];
    }

    // -------------------------------------------------------------------------
    // RECORD RESOLUTION – skip if kode_dosen or email already exists
    // -------------------------------------------------------------------------
    public function resolveRecord(): ?Dosen
    {
        // Cek duplikasi kode_dosen
        $kodeExists = Dosen::where('kode_dosen', $this->data['kode_dosen'])->exists();
        if ($kodeExists) {
            throw new RowImportFailedException(
                "Kode Dosen [{$this->data['kode_dosen']}] sudah terdaftar. Baris dilewati."
            );
        }

        // Cek duplikasi email pada tabel users
        $emailExists = User::where('email', $this->data['email'])->exists();
        if ($emailExists) {
            throw new RowImportFailedException(
                "Email [{$this->data['email']}] sudah terdaftar. Baris dilewati."
            );
        }

        return new Dosen;
    }

    // -------------------------------------------------------------------------
    // LIFECYCLE HOOK – buat akun User + assign role before inserting Dosen
    // -------------------------------------------------------------------------
    protected function beforeCreate(): void
    {
        // Buat akun User dengan password default = kode_dosen
        $user = User::create([
            'name' => $this->data['nama_lengkap'],
            'email' => $this->data['email'],
            'password' => Hash::make($this->data['kode_dosen']),
        ]);

        // Assign role 'dosen' via Spatie Permission
        $user->assignRole('dosen');

        // Inject user_id ke record Dosen sebelum disimpan
        $this->record->user_id = $user->id;
    }

    // -------------------------------------------------------------------------
    // CUSTOM NOTIFICATION – pesan selesai import (Bahasa Indonesia)
    // -------------------------------------------------------------------------
    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Import Data Dosen Selesai';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $successful = $import->successful_rows;
        $failed = $import->getFailedRowsCount();
        $body = "{$successful} data dosen berhasil diimport.";
        if ($failed > 0) {
            $body .= " {$failed} baris dilewati (duplikat kode_dosen/email atau data tidak valid).";
        }
        return $body;
    }
}
