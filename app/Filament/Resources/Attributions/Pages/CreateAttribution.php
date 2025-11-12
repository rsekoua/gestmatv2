<?php

namespace App\Filament\Resources\Attributions\Pages;

use App\Filament\Resources\Attributions\AttributionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribution extends CreateRecord
{
    protected static string $resource = AttributionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
