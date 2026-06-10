<?php

namespace App\Filament\Resources\Mahasiswas\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudyPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'studyPlans';

    protected static ?string $title = 'Rencana Studi';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course.code')
                    ->label('Kode MK')
                    ->searchable(),

                TextColumn::make('course.credits')
                    ->label('SKS')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('semester', 'asc');
    }
}
