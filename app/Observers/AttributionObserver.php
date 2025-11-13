<?php

namespace App\Observers;

use App\Models\Attribution;
use App\Models\Materiel;
use Illuminate\Validation\ValidationException;

class AttributionObserver
{
    /**
     * Handle the Attribution "creating" event.
     */
    public function creating(Attribution $attribution): void
    {
        // Récupérer le matériel
        $materiel = Materiel::find($attribution->materiel_id);

        if (! $materiel) {
            throw ValidationException::withMessages([
                'materiel_id' => 'Le matériel spécifié n\'existe pas.',
            ]);
        }

        // Vérifier que le matériel n'est pas rebuté
        if ($materiel->statut === 'rebuté') {
            throw ValidationException::withMessages([
                'materiel_id' => 'Ce matériel est rebuté et ne peut pas être attribué.',
            ]);
        }

        // Vérifier que le matériel n'est pas déjà attribué
        $existingAttribution = Attribution::where('materiel_id', $attribution->materiel_id)
            ->whereNull('date_restitution')
            ->exists();

        if ($existingAttribution) {
            throw ValidationException::withMessages([
                'materiel_id' => 'Ce matériel est déjà attribué à un autre employé.',
            ]);
        }

        // Vérifier que le statut du matériel permet l'attribution
        if ($materiel->statut === 'attribué') {
            throw ValidationException::withMessages([
                'materiel_id' => 'Ce matériel a déjà le statut "attribué".',
            ]);
        }

        // Mettre à jour le statut du matériel à "attribué"
        if ($attribution->materiel_id) {
            Materiel::withoutEvents(function () use ($attribution) {
                Materiel::where('id', $attribution->materiel_id)
                    ->update(['statut' => 'attribué']);
            });
        }
    }

    /**
     * Handle the Attribution "created" event.
     */
    public function created(Attribution $attribution): void
    {
        //
    }

    /**
     * Handle the Attribution "updating" event.
     */
    public function updating(Attribution $attribution): void
    {
        // Si on ajoute une date de restitution (clôture de l'attribution)
        if ($attribution->isDirty('date_restitution') && ! is_null($attribution->date_restitution)) {
            // Valider que la date de restitution est >= date d'attribution
            if ($attribution->date_restitution < $attribution->date_attribution) {
                throw ValidationException::withMessages([
                    'date_restitution' => 'La date de restitution doit être postérieure à celle de l\'attribution.',
                ]);
            }

            // Valider que les champs obligatoires sont présents
            if (empty($attribution->observations_res)) {
                throw ValidationException::withMessages([
                    'observations_res' => 'Les observations de restitution sont obligatoires.',
                ]);
            }


            if (empty($attribution->etat_general_res)) {
                throw ValidationException::withMessages([
                    'etat_general_res' => 'L\'état général est obligatoire lors de la restitution.',
                ]);
            }

            if (empty($attribution->etat_fonctionnel_res)) {
                throw ValidationException::withMessages([
                    'etat_fonctionnel_res' => 'L\'état fonctionnel est obligatoire lors de la restitution.',
                ]);
            }

            $this->handleRestitution($attribution);
        }
    }

    /**
     * Handle the Attribution "updated" event.
     */
    public function updated(Attribution $attribution): void
    {
        //
    }

    /**
     * Handle the Attribution "deleted" event.
     */
    public function deleted(Attribution $attribution): void
    {
        // Si l'attribution est supprimée et qu'elle était active, remettre le matériel disponible
        if (is_null($attribution->date_restitution) && $attribution->materiel_id) {
            Materiel::withoutEvents(function () use ($attribution) {
                Materiel::where('id', $attribution->materiel_id)
                    ->update(['statut' => 'disponible']);
            });
        }
    }

    /**
     * Handle the Attribution "restored" event.
     */
    public function restored(Attribution $attribution): void
    {
        //
    }

    /**
     * Handle the Attribution "force deleted" event.
     */
    public function forceDeleted(Attribution $attribution): void
    {
        //
    }

    /**
     * Handle restitution logic.
     */
    protected function handleRestitution(Attribution $attribution): void
    {
        if (! $attribution->materiel_id) {
            return;
        }

        // Mettre à jour le statut du matériel selon la décision de restitution
        $newStatus = match ($attribution->decision_res) {
            'remis_en_stock' => 'disponible',
            'a_reparer' => 'en_maintenance',
            'rebut' => 'rebuté',
            default => 'disponible',
        };

        // Si pas de décision mais état fonctionnel défini
        if (is_null($attribution->decision_res) && $attribution->etat_fonctionnel_res) {
            $newStatus = match ($attribution->etat_fonctionnel_res) {
                'parfait', 'defauts_mineurs' => 'disponible',
                'dysfonctionnements' => 'en_maintenance',
                'hors_service' => 'en_panne',
                default => 'disponible',
            };
        }

        Materiel::withoutEvents(function () use ($attribution, $newStatus) {
            Materiel::where('id', $attribution->materiel_id)
                ->update(['statut' => $newStatus]);
        });
    }
}
