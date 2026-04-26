<?php

namespace App\Filament\Resources\StudentProgress\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentProgressInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Detail Progres Studi')
                    ->schema([

                        TextEntry::make('user.name')
                            ->label('Mahasiswa'),

                        TextEntry::make('total_sks')
                            ->label('Total SKS'),

                        TextEntry::make('ipk')
                            ->label('IPK')
                            ->badge(),

                        TextEntry::make('passed_courses')
                            ->label('Mata Kuliah Lulus'),

                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Update')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
