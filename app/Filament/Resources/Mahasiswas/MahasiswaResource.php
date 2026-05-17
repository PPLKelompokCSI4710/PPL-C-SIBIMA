<?php

namespace App\Filament\Resources\Mahasiswas;

use App\Filament\Resources\Mahasiswas\Pages\CreateMahasiswa;
use App\Filament\Resources\Mahasiswas\Pages\EditMahasiswa;
use App\Filament\Resources\Mahasiswas\Pages\ListMahasiswas;
use App\Filament\Resources\Mahasiswas\Pages\ViewMahasiswa;
use App\Filament\Resources\Mahasiswas\Schemas\MahasiswaForm;
use App\Filament\Resources\Mahasiswas\Schemas\MahasiswaInfolist;
use App\Filament\Resources\Mahasiswas\Tables\MahasiswasTable;
use App\Models\Mahasiswa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class MahasiswaResource extends Resource
{
    protected static ?string $model = Mahasiswa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function getNavigationLabel(): string
    {
        return 'Data Mahasiswa';
    }

    public static function getModelLabel(): string
    {
        return 'Mahasiswa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Mahasiswa';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Manajemen Pengguna';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    // =========================================================================
    // FORM (PBI 1: Create — PBI 2: Edit)
    // =========================================================================

    public static function form(Schema $schema): Schema
    {
        return MahasiswaForm::configure($schema);
    }

    // =========================================================================
    // INFOLIST (Read-only View Page)
    // =========================================================================

    public static function infolist(Schema $schema): Schema
    {
        return MahasiswaInfolist::configure($schema);
    }

    // =========================================================================
    // TABLE (PBI 3: List, Search, Filter, Sort — PBI 2: Delete)
    // =========================================================================

    public static function table(Table $table): Table
    {
        return MahasiswasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // =========================================================================
    // SOFT DELETE SCOPE
    // =========================================================================

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    // =========================================================================
    // PAGES
    // =========================================================================

    public static function getPages(): array
    {
        return [
            'index' => ListMahasiswas::route('/'),
            'create' => CreateMahasiswa::route('/create'),
            'view' => ViewMahasiswa::route('/{record}'),
            'edit' => EditMahasiswa::route('/{record}/edit'),
        ];
    }
}
