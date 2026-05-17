<?php

namespace App\Filament\Resources\Mahasiswas\Schemas;

use App\Enums\AkademikStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MahasiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Section 1: Akun Pengguna ────────────────────────────────
                Section::make('Akun Pengguna')
                    ->description('Masukkan email mahasiswa. Sistem akan membuat akun login secara otomatis.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email Mahasiswa')
                            ->placeholder('contoh@email.com')
                            ->email()
                            ->required()
                            ->unique(table: 'users', column: 'email')
                            ->validationMessages([
                                'unique' => 'Email ini sudah terdaftar sebagai akun pengguna.',
                            ])
                            ->helperText('Password default akan di-set menggunakan NIM mahasiswa.')
                            ->hiddenOn('edit'),
                    ])
                    ->columnSpanFull()
                    ->hiddenOn('edit'),

                // ─── Section 2: Data Akademik ────────────────────────────────
                Section::make('Data Akademik')
                    ->description('Informasi akademik resmi mahasiswa.')
                    ->icon('heroicon-o-book-open')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nim')
                            ->label('NIM')
                            ->placeholder('Contoh: 2024001001')
                            ->required()
                            ->maxLength(20)
                            ->unique(table: 'mahasiswa', column: 'nim', ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'NIM ini sudah terdaftar.',
                            ]),

                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->placeholder('Nama sesuai KTP')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('program_studi')
                            ->label('Program Studi')
                            ->placeholder('Contoh: Teknik Informatika')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('fakultas')
                            ->label('Fakultas')
                            ->placeholder('Contoh: Fakultas Ilmu Komputer')
                            ->maxLength(255),

                        TextInput::make('angkatan')
                            ->label('Angkatan')
                            ->placeholder('Contoh: 2024')
                            ->required()
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(date('Y') + 1)
                            ->maxLength(4)
                            ->minLength(4),

                        TextInput::make('semester')
                            ->label('Semester')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(14)
                            ->default(1),

                        TextInput::make('ipk')
                            ->label('IPK')
                            ->placeholder('0.00')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(4)
                            ->step(0.01),

                        Select::make('status_akademik')
                            ->label('Status Akademik')
                            ->options(AkademikStatus::class)
                            ->required()
                            ->default(AkademikStatus::AKTIF)
                            ->native(false),

                        TextInput::make('sks_lulus')
                            ->label('SKS Lulus')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        TextInput::make('sks_total')
                            ->label('Total SKS')
                            ->numeric()
                            ->minValue(0)
                            ->default(144),

                        Toggle::make('status_kelulusan_bimbingan')
                            ->label('Lulus Bimbingan?')
                            ->helperText('Aktifkan jika mahasiswa dinyatakan lulus bimbingan.')
                            ->default(false)
                            ->columnSpanFull(),
                    ]),

                // ─── Section 3: Data Pribadi ─────────────────────────────────
                Section::make('Data Pribadi')
                    ->description('Informasi kontak dan identitas diri mahasiswa.')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('no_telepon')
                            ->label('Nomor Telepon')
                            ->placeholder('Contoh: 08123456789')
                            ->tel()
                            ->maxLength(20),

                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->maxDate(now()),

                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->placeholder('Alamat lengkap mahasiswa')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('foto')
                            ->label('Foto Profil')
                            ->image()
                            ->imageEditor()
                            ->directory('foto-mahasiswa')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Format: JPG, PNG. Maks. 2MB.')
                            ->columnSpanFull(),
                    ]),

            ]);
    }
}
