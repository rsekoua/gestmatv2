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

        if (! empty($accessories)) {
            // Préparer les données pivot pour chaque accessoire
            $pivotData = [];
            foreach ($accessories as $accessoryId) {
                $pivotData[$accessoryId] = [
                    'statut_att' => 'fourni',
                    'statut_res' => null,
                ];
            }

            // Attacher les accessoires avec les données pivot
            $this->record->accessories()->attach($pivotData);
        }
    }
}
