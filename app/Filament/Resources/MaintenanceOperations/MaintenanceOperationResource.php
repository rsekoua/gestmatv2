<?php

namespace App\Filament\Resources\MaintenanceOperations;

use App\Filament\Resources\MaintenanceOperations\Pages\CreateMaintenanceOperation;
use App\Filament\Resources\MaintenanceOperations\Pages\EditMaintenanceOperation;
use App\Filament\Resources\MaintenanceOperations\Pages\ListMaintenanceOperations;
use App\Filament\Resources\MaintenanceOperations\Schemas\MaintenanceOperationForm;
use App\Filament\Resources\MaintenanceOperations\Tables\MaintenanceOperationsTable;
use App\Models\MaintenanceOperation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaintenanceOperationResource extends Resource
{
    protected static ?string $model = MaintenanceOperation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MaintenanceOperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceOperationsTable::configure($table);
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
            'index' => ListMaintenanceOperations::route('/'),
            'create' => CreateMaintenanceOperation::route('/create'),
            'edit' => EditMaintenanceOperation::route('/{record}/edit'),
        ];
    }
}
