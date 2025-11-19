<?php

namespace App\Filament\Resources\MaintenanceOperations\Pages;

use App\Filament\Resources\MaintenanceOperations\MaintenanceOperationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceOperations extends ListRecords
{
    protected static string $resource = MaintenanceOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
