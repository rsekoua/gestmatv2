<?php

namespace App\Filament\Resources\MaintenanceDefinitions;

use App\Filament\Resources\MaintenanceDefinitions\Pages\CreateMaintenanceDefinition;
use App\Filament\Resources\MaintenanceDefinitions\Pages\EditMaintenanceDefinition;
use App\Filament\Resources\MaintenanceDefinitions\Pages\ListMaintenanceDefinitions;
use App\Filament\Resources\MaintenanceDefinitions\Schemas\MaintenanceDefinitionForm;
use App\Filament\Resources\MaintenanceDefinitions\Tables\MaintenanceDefinitionsTable;
use App\Models\MaintenanceDefinition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaintenanceDefinitionResource extends Resource
{
    protected static ?string $model = MaintenanceDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MaintenanceDefinitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceDefinitionsTable::configure($table);
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
            'index' => ListMaintenanceDefinitions::route('/'),
            'create' => CreateMaintenanceDefinition::route('/create'),
            'edit' => EditMaintenanceDefinition::route('/{record}/edit'),
        ];
    }
}
