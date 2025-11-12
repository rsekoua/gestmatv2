<?php

namespace App\Observers;

use App\Models\Attribution;
use App\Models\Materiel;

class AttributionObserver
{
    /**
     * Handle the Attribution "creating" event.
     */
    public function creating(Attribution $attribution): void
    {
        // Vérifier que le matériel n'est pas déjà attribué
        $existingAttribution = Attribution::where('materiel_id', $attribution->materiel_id)
            ->whereNull('date_restitution')
            ->exists();

        if ($existingAttribution) {
            throw new \Exception('Ce matériel est déjà attribué à un autre employé.');
        }

        // Mettre à jour le statut du matériel à "attribué"
        if ($attribution->materiel_id) {
            Materiel::where('id', $attribution->materiel_id)
                ->update(['statut' => 'attribué']);
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
        if ($attribution->isDirty('date_restitution') && !is_null($attribution->date_restitution)) {
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
            Materiel::where('id', $attribution->materiel_id)
                ->update(['statut' => 'disponible']);
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
        if (!$attribution->materiel_id) {
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

        Materiel::where('id', $attribution->materiel_id)
            ->update(['statut' => $newStatus]);
    }
}
