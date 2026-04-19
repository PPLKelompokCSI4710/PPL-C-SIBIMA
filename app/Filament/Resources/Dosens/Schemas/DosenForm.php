<?php

namespace App\Filament\Resources\Dosens\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class DosenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Section 1: Akun Pengguna ────────────────────────────────
                Section::make('Akun Pengguna')
                    ->description('Hubungkan profil ini dengan akun login sistem (Role Dosen).')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Select::make('user_id')
                            ->label('Akun User')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'email',
                                modifyQueryUsing: fn (Builder $query) => $query->role('dosen'),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (User $record) => "{$record->name} ({$record->email})"
                            )
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required()
                            ->unique(table: 'dosen', column: 'user_id', ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'Akun user ini sudah terhubung dengan profil dosen lain.',
                            ]),
                    ])
                    ->columnSpanFull(),

                // ─── Section 2: Data Akademik ────────────────────────────────
                Section::make('Data Akademik Dosen')
                    ->description('Informasi NIDN dan Program Studi pengajaran.')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nidn')
                            ->label('NIDN')
                            ->required()
                            ->maxLength(255)
                            ->unique(table: 'dosen', column: 'nidn', ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'NIDN ini sudah terdaftar.',
                            ]),

                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap (beserta gelar)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('program_studi')
                            ->label('Program Studi')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('fakultas')
                            ->label('Fakultas')
                            ->maxLength(255),

                        TextInput::make('jabatan_fungsional')
                            ->label('Jabatan Fungsional')
                            ->maxLength(255),

                        TextInput::make('gelar')
                            ->label('Gelar Akademik')
                            ->maxLength(255),

                        TextInput::make('kuota_mahasiswa')
                            ->label('Kuota Pembimbingan Mahasiswa')
                            ->required()
                            ->numeric()
                            ->default(10)
                            ->minValue(1),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),

                // ─── Section 3: Data Pribadi ─────────────────────────────────
                Section::make('Informasi Pribadi & Kontak')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('no_telepon')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(255),

                        FileUpload::make('foto')
                            ->label('Foto Profil')
                            ->image()
                            ->imageEditor()
                            ->directory('foto-dosen')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        Textarea::make('bio')
                            ->label('Biografi Singkat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
