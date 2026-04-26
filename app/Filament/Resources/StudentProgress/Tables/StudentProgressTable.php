<?php

namespace App\Filament\Resources\StudentProgress\Tables;

use Filament\Actions\EditAction;
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
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.mahasiswa.nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_sks')
                    ->label('Progress Studi (SKS)')
                    ->numeric()
                    ->sortable()
                    ->description(fn ($record): string => 'IPK: '.$record->ipk),

                TextColumn::make('user.mahasiswa.status_akademik')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'aktif' => 'success',
                        'cuti' => 'warning',
                        'lulus' => 'info',
                        'drop_out', 'mengulang' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state ?? 'Belum ada status'))),

                TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->date('d M Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()->label('Detail'),
                EditAction::make()->label('Edit'),
            ])
            ->headerActions([])
            ->emptyStateHeading('Belum ada data progres studi')
            ->emptyStateDescription('Silakan tambahkan data mahasiswa.');
    }
}
