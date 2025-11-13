<?php

namespace App\Observers;

use App\Models\Attribution;
use App\Models\Materiel;
use Illuminate\Validation\ValidationException;

class MaterielObserver
{
    /**
     * Handle the Materiel "updating" event.
     */
    public function updating(Materiel $materiel): void
    {
        // Vérifier si le statut est modifié
        if ($materiel->isDirty('statut')) {
            // Vérifier si le matériel a une attribution active
            $hasActiveAttribution = Attribution::where('materiel_id', $materiel->id)
                ->whereNull('date_restitution')
                ->exists();

            if ($hasActiveAttribution) {
                // Exception de validation pour afficher l'erreur sur le champ
                throw ValidationException::withMessages([
                    'statut' => 'Ce matériel a une attribution active. Le statut ne peut pas être modifié manuellement.',
                ]);
            }
        }
    }

    /**
     * Handle the Materiel "creating" event.
     */
    public function creating(Materiel $materiel): void
    {
        // Validation lors de la création
        if (in_array($materiel->statut, ['attribué'])) {
            $hasActiveAttribution = Attribution::where('materiel_id', $materiel->id)
                ->whereNull('date_restitution')
                ->exists();

            if (! $hasActiveAttribution) {
                // Si on tente de créer un matériel avec statut "attribué" sans attribution active
                // On le met en disponible par défaut
                $materiel->statut = 'disponible';
            }
        }
    }

    /**
     * Handle the Materiel "deleting" event.
     */
    public function deleting(Materiel $materiel): void
    {
        // Vérifier si le matériel a des attributions
        $hasAttributions = Attribution::where('materiel_id', $materiel->id)->exists();

        if ($hasAttributions) {
            // Compter les attributions actives et fermées
            $activeCount = Attribution::where('materiel_id', $materiel->id)
                ->whereNull('date_restitution')
                ->count();
            $totalCount = Attribution::where('materiel_id', $materiel->id)->count();

            if ($activeCount > 0) {
                throw ValidationException::withMessages([
                    'materiel' => "Impossible de supprimer ce matériel : il a {$activeCount} attribution(s) active(s). Veuillez d'abord restituer le matériel.",
                ]);
            }

            throw ValidationException::withMessages([
                'materiel' => "Impossible de supprimer ce matériel : il a {$totalCount} attribution(s) dans l'historique. Les données d'attribution doivent être préservées.",
            ]);
        }
    }
}
