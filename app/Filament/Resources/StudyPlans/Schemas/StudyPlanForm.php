<?php

namespace App\Filament\Resources\StudyPlans\Schemas;

use App\Models\Course;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StudyPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('mahasiswa_id')
                    ->relationship('mahasiswa', 'nama_lengkap')
                    ->required()
                    ->default(fn () => auth()->user()->hasRole('Mahasiswa') ? auth()->user()->mahasiswa?->id : null)
                    ->hidden(fn () => auth()->user()->hasRole('Mahasiswa')),
                Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required()
                    ->live() // Update when changed
                    ->afterStateUpdated(function ($set, $state) {
                        $course = Course::find($state);
                        if ($course) {
                            // Can pre-fill semester if needed, or show credits
                        }
                    }),
                Select::make('semester')
                    ->options(array_combine(range(1, 8), range(1, 8)))
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->visible(fn () => ! auth()->user()->hasRole('Mahasiswa')),
            ]);
    }
}
