<?php

namespace App\Filament\Resources\StudyPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudyPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mahasiswa.nama_lengkap')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => ! auth()->user()->hasRole('Mahasiswa')),
                TextColumn::make('course.name')
                    ->label('Course Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.code')
                    ->label('Course Code')
                    ->searchable(),
                TextColumn::make('course.credits')
                    ->label('Credits (SKS)')
                    ->numeric(),
                TextColumn::make('semester')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
