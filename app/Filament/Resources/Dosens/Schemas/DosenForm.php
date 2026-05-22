<?php

namespace App\Filament\Resources\Dosens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DosenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil & Akademik Dosen')
                    ->description('Kelola identitas akun dan data pengajaran dosen pembimbing.')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(table: 'users', column: 'email', ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->rule(Password::default()),

                        TextInput::make('program_studi')
                            ->label('Program Studi')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('fakultas')
                            ->label('Fakultas')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('kuota_pembimbingan')
                            ->label('Kapasitas Bimbingan')
                            ->numeric()
                            ->default(10)
                            ->required()
                            ->minValue(1),
                    ]),
            ]);
    }
}
