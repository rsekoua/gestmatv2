<?php

namespace App\Filament\Resources\Attributions\Pages;

use App\Filament\Concerns\ManagesAccessories;
use App\Filament\Resources\Attributions\AttributionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribution extends CreateRecord
{
    use ManagesAccessories;

    protected static string $resource = AttributionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    /**
     * Mutate form data before creating the record.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extraire les accessoires du tableau de données
        // car ils seront gérés séparément dans afterCreate
        if (isset($data['accessories'])) {
            unset($data['accessories']);
        }

        return $data;
    }

    /**
     * Handle actions after record creation.
     */
    protected function afterCreate(): void
    {
        // Récupérer les accessoires sélectionnés depuis le formulaire
        $accessories = $this->form->getState()['accessories'] ?? [];

        // Utiliser le trait pour attacher les accessoires
        $this->attachAccessories($this->record, $accessories);
    }
}
