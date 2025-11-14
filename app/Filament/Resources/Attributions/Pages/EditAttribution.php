<?php

namespace App\Filament\Resources\Attributions\Pages;

use App\Filament\Actions\RestituerAttributionAction;
use App\Filament\Resources\Attributions\AttributionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAttribution extends EditRecord
{
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
        $data['accessories'] = $this->record->accessories()->pluck('accessory_id')->toArray();

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

        // Préparer les données pivot pour chaque accessoire
        $pivotData = [];
        foreach ($accessories as $accessoryId) {
            // Vérifier si l'accessoire était déjà attaché
            $existingPivot = $this->record->accessories()
                ->wherePivot('accessory_id', $accessoryId)
                ->first();

            if ($existingPivot) {
                // Conserver les données pivot existantes
                $pivotData[$accessoryId] = [
                    'statut_att' => $existingPivot->pivot->statut_att,
                    'statut_res' => $existingPivot->pivot->statut_res,
                ];
            } else {
                // Nouvel accessoire, définir les valeurs par défaut
                $pivotData[$accessoryId] = [
                    'statut_att' => 'fourni',
                    'statut_res' => null,
                ];
            }
        }

        // Synchroniser les accessoires avec les données pivot
        $this->record->accessories()->sync($pivotData);
    }
}
