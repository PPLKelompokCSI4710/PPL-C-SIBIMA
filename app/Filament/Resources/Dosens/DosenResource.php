<?php

namespace App\Filament\Resources\Dosens;

use App\Filament\Resources\Dosens\Pages\CreateDosen;
use App\Filament\Resources\Dosens\Pages\EditDosen;
use App\Filament\Resources\Dosens\Pages\ListDosens;
use App\Filament\Resources\Dosens\Pages\ViewDosen;
use App\Filament\Resources\Dosens\Schemas\DosenForm;
use App\Filament\Resources\Dosens\Tables\DosensTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DosenResource extends Resource
{
    // Sekarang menggunakan model User sebagai basis
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

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

    // Filter agar Resource ini hanya mengelola User dengan role 'dosen'
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role('dosen');
    }

    public static function form(Schema $schema): Schema
    {
        return DosenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DosensTable::configure($table);
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
}
