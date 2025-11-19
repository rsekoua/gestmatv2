<?php

namespace App\Filament\Resources\MaintenanceDefinitions\Pages;

use App\Filament\Resources\MaintenanceDefinitions\MaintenanceDefinitionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceDefinition extends EditRecord
{
    protected static string $resource = MaintenanceDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
