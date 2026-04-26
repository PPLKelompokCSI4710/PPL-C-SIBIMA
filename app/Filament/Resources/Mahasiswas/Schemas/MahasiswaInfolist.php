<?php

namespace App\Filament\Resources\Mahasiswas\Schemas;

use App\Models\Mahasiswa;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MahasiswaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Section: Akun Pengguna ───────────────────────────────────
                Section::make('Akun Pengguna')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Nama Akun'),
                        TextEntry::make('user.email')
                            ->label('Email Akun'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ─── Section: Data Akademik ───────────────────────────────────
                Section::make('Data Akademik')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        TextEntry::make('nim')
                            ->label('NIM')
                            ->copyable(),
                        TextEntry::make('nama_lengkap')
                            ->label('Nama Lengkap'),
                        TextEntry::make('program_studi')
                            ->label('Program Studi'),
                        TextEntry::make('fakultas')
                            ->label('Fakultas')
                            ->placeholder('-'),
                        TextEntry::make('angkatan')
                            ->label('Angkatan')
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('semester')
                            ->label('Semester'),
                        TextEntry::make('ipk')
                            ->label('IPK')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('status_akademik')
                            ->label('Status Akademik')
                            ->badge(),
                        TextEntry::make('sks_lulus')
                            ->label('SKS Lulus')
                            ->numeric(),
                        TextEntry::make('sks_total')
                            ->label('Total SKS')
                            ->numeric(),
                        IconEntry::make('status_kelulusan_bimbingan')
                            ->label('Lulus Bimbingan')
                            ->boolean(),
                    ])
                    ->columns(2),

                // ─── Section: Data Pribadi ────────────────────────────────────
                Section::make('Data Pribadi')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextEntry::make('no_telepon')
                            ->label('Nomor Telepon')
                            ->placeholder('-'),
                        TextEntry::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->date('d F Y')
                            ->placeholder('-'),
                        TextEntry::make('alamat')
                            ->label('Alamat')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        ImageEntry::make('foto')
                            ->label('Foto Profil')
                            ->circular()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // ─── Section: Riwayat (hanya tampil jika di-soft delete) ──────
                Section::make('Riwayat')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime('d M Y, H:i')
                            ->visible(fn (Mahasiswa $record): bool => $record->trashed()),
                    ])
                    ->columns(2)
                    ->collapsed(),

            ]);
    }
}
