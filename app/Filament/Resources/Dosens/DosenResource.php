<?php

namespace App\Filament\Resources\Dosens;

use App\Filament\Resources\Dosens\Pages\CreateDosen;
use App\Filament\Resources\Dosens\Pages\EditDosen;
use App\Filament\Resources\Dosens\Pages\ListDosens;
use App\Filament\Resources\Dosens\Pages\ViewDosen;
use App\Filament\Resources\Dosens\Schemas\DosenForm;
use App\Filament\Resources\Dosens\Tables\DosensTable;
use App\Models\Dosen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DosenResource extends Resource
{
    protected static ?string $model = Dosen::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function getNavigationLabel(): string
    {
        return 'Data Dosen';
    }

    public static function getModelLabel(): string
    {
        return 'Dosen';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Dosen';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Manajemen Pengguna';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return DosenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DosensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDosens::route('/'),
            'create' => CreateDosen::route('/create'),
            'view' => ViewDosen::route('/{record}'),
            'edit' => EditDosen::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
