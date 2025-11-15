<?php

namespace App\Filament\Resources\Attributions\Pages;

use App\Filament\Actions\RestituerAttributionAction;
use App\Filament\Concerns\ManagesAccessories;
use App\Filament\Resources\Attributions\AttributionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAttribution extends EditRecord
{
    use ManagesAccessories;

    protected static string $resource = AttributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RestituerAttributionAction::make(),
            ViewAction::make()
                ->icon(Heroicon::Eye),
            DeleteAction::make()
                ->icon(Heroicon::Trash)
                ->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    /**
     * Mutate form data before filling the form.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Charger les accessoires existants pour les afficher dans le formulaire
        $data['accessories'] = $this->getAccessoryIds($this->record);

        return $data;
    }

    /**
     * Mutate form data before saving.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extraire les accessoires du tableau de données
        // car ils seront gérés séparément dans afterSave
        if (isset($data['accessories'])) {
            unset($data['accessories']);
        }

        return $data;
    }

    /**
     * Handle actions after record save.
     */
    protected function afterSave(): void
    {
        // Récupérer les accessoires sélectionnés depuis le formulaire
        $accessories = $this->form->getState()['accessories'] ?? [];

        // Utiliser le trait pour synchroniser les accessoires
        $this->syncAccessories($this->record, $accessories);
    }
}
