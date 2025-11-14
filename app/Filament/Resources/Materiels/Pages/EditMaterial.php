<?php

namespace App\Filament\Resources\Materiels\Pages;

use App\Filament\Resources\Materiels\MaterialResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditMaterial extends EditRecord
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::Eye)->size('sm'),
            DeleteAction::make()
                ->icon(Heroicon::Trash)
                ->requiresConfirmation()
                ->size('sm')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Matériel supprimé')
                        ->body('Le matériel a été supprimé avec succès.')
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Matériel mis à jour')
            ->body('Les modifications ont été enregistrées avec succès.');
    }
}
