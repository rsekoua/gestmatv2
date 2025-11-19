<?php

namespace App\Filament\Resources\MaintenanceOperations\Pages;

use App\Filament\Resources\MaintenanceOperations\MaintenanceOperationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceOperation extends CreateRecord
{
    protected static string $resource = MaintenanceOperationResource::class;
}
