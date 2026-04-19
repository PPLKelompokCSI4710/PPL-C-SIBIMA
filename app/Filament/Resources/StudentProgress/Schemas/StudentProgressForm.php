<?php

namespace App\Filament\Resources\StudentProgress\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentProgressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('user_id')
                    ->label('Mahasiswa')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('total_sks')
                    ->label('Total SKS')
                    ->numeric()
                    ->required(),

                TextInput::make('ipk')
                    ->label('IPK')
                    ->numeric()
                    ->step(0.01)
                    ->required(),

                TextInput::make('passed_courses')
                    ->label('Mata Kuliah Lulus')
                    ->numeric()
                    ->required(),
            ]);
    }
}
