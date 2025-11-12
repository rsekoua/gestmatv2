<?php

namespace App\Filament\Resources\Materiels\Pages;

use App\Filament\Resources\Materiels\MaterialResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMaterial extends CreateRecord
{
    protected static string $resource = MaterialResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Matériel créé avec succès')
            ->body('Le matériel a été créé et enregistré dans la base de données.');
    }
}
