<?php

namespace App\Filament\Resources\StudentProgress;

use App\Filament\Resources\StudentProgress\Pages\CreateStudentProgress;
use App\Filament\Resources\StudentProgress\Pages\EditStudentProgress;
use App\Filament\Resources\StudentProgress\Pages\ListStudentProgress;
use App\Filament\Resources\StudentProgress\Pages\ViewStudentProgress;
use App\Filament\Resources\StudentProgress\Schemas\StudentProgressForm;
use App\Filament\Resources\StudentProgress\Schemas\StudentProgressInfolist;
use App\Filament\Resources\StudentProgress\Tables\StudentProgressTable;
use App\Models\StudentProgress;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentProgressResource extends Resource
{
    protected static ?string $model = StudentProgress::class;

    protected static ?string $navigationLabel = 'Progres Studi';

    protected static ?string $pluralModelLabel = 'Progres Studi';

    protected static ?string $modelLabel = 'Progres Studi';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        if (auth()->check() && auth()->user()->hasRole('mahasiswa')) {
            return $record->user_id === auth()->id();
        }

        return true;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    /**
     * Mahasiswa hanya lihat data sendiri
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->hasRole('mahasiswa')) {
            return $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return StudentProgressForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentProgressInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentProgressTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentProgress::route('/'),
            'create' => CreateStudentProgress::route('/create'),
            'view' => ViewStudentProgress::route('/{record}'),
            'edit' => EditStudentProgress::route('/{record}/edit'),
        ];
    }
}
