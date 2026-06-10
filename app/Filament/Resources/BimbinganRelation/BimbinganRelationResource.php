<?php

namespace App\Filament\Resources\BimbinganRelation;

use App\Filament\Resources\BimbinganRelation\Pages\EditBimbinganRelation;
use App\Filament\Resources\BimbinganRelation\Pages\ListBimbinganRelation;
use App\Filament\Resources\BimbinganRelation\Schemas\BimbinganRelationForm;
use App\Filament\Resources\BimbinganRelation\Tables\BimbinganRelationTable;
use App\Models\Mahasiswa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class BimbinganRelationResource extends Resource
{
    protected static ?string $model = Mahasiswa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function getNavigationLabel(): string
    {
        return 'Penetapan Pembimbing';
    }

    public static function getModelLabel(): string
    {
        return 'Penetapan Pembimbing';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penetapan Pembimbing';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Manajemen Pengguna';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    // =========================================================================
    // FORM (Penetapan Dosen Pembimbing)
    // =========================================================================

    public static function form(Schema $schema): Schema
    {
        return BimbinganRelationForm::configure($schema);
    }

    // =========================================================================
    // TABLE (Daftar Mahasiswa & Pembimbing)
    // =========================================================================

    public static function table(Table $table): Table
    {
        return BimbinganRelationTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
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
            'index' => ListBimbinganRelation::route('/'),
            'edit' => EditBimbinganRelation::route('/{record}/edit'),
        ];
    }
}
