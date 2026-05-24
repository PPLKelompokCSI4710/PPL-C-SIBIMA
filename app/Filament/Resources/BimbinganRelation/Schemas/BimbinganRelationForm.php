<?php

namespace App\Filament\Resources\BimbinganRelation\Schemas;

use App\Models\Dosen;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BimbinganRelationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Section: Identitas Mahasiswa (Read-only) ─────────────
                Section::make('Identitas Mahasiswa')
                    ->description('Informasi mahasiswa yang akan ditetapkan dosen pembimbingnya.')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nim')
                            ->label('NIM')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('program_studi')
                            ->label('Program Studi')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('angkatan')
                            ->label('Angkatan')
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                // ─── Section: Penetapan Dosen Pembimbing ──────────────────
                Section::make('Penetapan Dosen Pembimbing')
                    ->description('Pilih dosen pembimbing utama untuk mahasiswa ini.')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Select::make('dosen_id')
                            ->label('Dosen Pembimbing')
                            ->relationship(
                                name: 'dosen',
                                titleAttribute: 'nama_lengkap',
                                modifyQueryUsing: fn ($query) => $query->where('is_active', true),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Dosen $record) => "{$record->nidn} — {$record->nama_lengkap}"
                            )
                            ->searchable(['nama_lengkap', 'nidn'])
                            ->preload()
                            ->placeholder('Pilih Dosen Pembimbing...')
                            ->helperText('Hanya menampilkan dosen yang berstatus aktif.')
                            ->native(false)
                            ->nullable(),
                    ]),

            ]);
    }
}
