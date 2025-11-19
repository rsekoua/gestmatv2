<?php

namespace App\Filament\Resources\MaintenanceOperations\Pages;

use App\Filament\Resources\MaintenanceOperations\MaintenanceOperationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceOperation extends EditRecord
{
    protected static string $resource = MaintenanceOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
