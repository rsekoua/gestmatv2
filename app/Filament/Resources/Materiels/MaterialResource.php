<?php

namespace App\Filament\Resources\Materiels;

use App\Filament\Resources\Materiels\Pages\CreateMaterial;
use App\Filament\Resources\Materiels\Pages\EditMaterial;
use App\Filament\Resources\Materiels\Pages\ListMaterials;
use App\Filament\Resources\Materiels\Pages\ViewMaterial;
use App\Filament\Resources\Materiels\Schemas\MaterialForm;
use App\Filament\Resources\Materiels\Tables\MaterialsTable;
use App\Models\Materiel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MaterialResource extends Resource
{
    protected static ?string $model = Materiel::class;

//    protected static string|BackedEnum|null $navigationIcon = Heroicon::ComputerDesktop;

    protected static string|UnitEnum|null $navigationGroup = 'Gestion des Matériels';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Matériels';

    protected static ?string $modelLabel = 'Matériel';

    protected static ?string $pluralModelLabel = 'Matériels';

    protected static ?string $recordTitleAttribute = 'nom';

    /**
     * Optimize queries with eager loading to prevent N+1 problems.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['materielType', 'activeAttribution.employee.service', 'activeAttribution.service.chefService']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string
    {
        $available = static::getModel()::where('statut', 'disponible')->count();
        $total = static::getModel()::count();

        if ($total === 0) {
            return 'gray';
        }

        $percentage = ($available / $total) * 100;

        return match (true) {
            $percentage >= 50 => 'success',
            $percentage >= 25 => 'warning',
            default => 'danger',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $total = static::getModel()::count();
        $available = static::getModel()::where('statut', 'disponible')->count();
        $attributed = static::getModel()::where('statut', 'attribué')->count();

        return "{$total} matériels | {$available} disponibles | {$attributed} attribués";
    }

    /**
     * @throws \Exception
     */
    public static function form(Schema $schema): Schema
    {
        return MaterialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttributionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaterials::route('/'),
            'create' => CreateMaterial::route('/create'),
            'view' => ViewMaterial::route('/{record}'),
            'edit' => EditMaterial::route('/{record}/edit'),
        ];
    }
}
