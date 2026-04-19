<?php

namespace App\Filament\Resources\StudentProgress\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentProgressTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_sks')
                    ->label('Total SKS')
                    ->sortable(),

                TextColumn::make('ipk')
                    ->label('IPK')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 3.50 => 'success',
                        $state >= 3.00 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('passed_courses')
                    ->label('MK Lulus'),

                TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->date('d M Y'),
            ])
            ->recordActions([
                ViewAction::make()->label('Detail'),
            ])
            ->headerActions([])
            ->emptyStateHeading('Belum ada data progres studi')
            ->emptyStateDescription('Silakan tambahkan data mahasiswa.');
    }
}
