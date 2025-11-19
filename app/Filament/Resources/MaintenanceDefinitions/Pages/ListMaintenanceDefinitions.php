<?php

namespace App\Filament\Resources\MaintenanceDefinitions\Pages;

use App\Filament\Resources\MaintenanceDefinitions\MaintenanceDefinitionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceDefinitions extends ListRecords
{
    protected static string $resource = MaintenanceDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
