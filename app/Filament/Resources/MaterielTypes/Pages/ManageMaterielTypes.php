<?php

namespace App\Filament\Resources\MaterielTypes\Pages;

use App\Filament\Resources\MaterielTypes\MaterielTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMaterielTypes extends ManageRecords
{
    protected static string $resource = MaterielTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
