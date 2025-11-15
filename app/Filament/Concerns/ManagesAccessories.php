<?php

namespace App\Filament\Concerns;

use App\Models\Attribution;

trait ManagesAccessories
{
    /**
     * Attach accessories to an attribution with proper pivot data.
     */
    protected function attachAccessories(Attribution $attribution, array $accessoryIds): void
    {
        if (empty($accessoryIds)) {
            return;
        }

        // Préparer les données pivot pour chaque accessoire
        $pivotData = [];
        foreach ($accessoryIds as $accessoryId) {
            $pivotData[$accessoryId] = [
                'statut_att' => 'fourni',
                'statut_res' => null,
            ];
        }

        // Attacher les accessoires avec les données pivot
        $attribution->accessories()->attach($pivotData);
    }

    /**
     * Sync accessories for an attribution, preserving existing pivot data.
     */
    protected function syncAccessories(Attribution $attribution, array $accessoryIds): void
    {
        // Préparer les données pivot pour chaque accessoire
        $pivotData = [];
        foreach ($accessoryIds as $accessoryId) {
            // Vérifier si l'accessoire était déjà attaché
            $existingPivot = $attribution->accessories()
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
        $attribution->accessories()->sync($pivotData);
    }

    /**
     * Get accessory IDs from an attribution.
     */
    protected function getAccessoryIds(Attribution $attribution): array
    {
        return $attribution->accessories()->pluck('accessory_id')->toArray();
    }
}
