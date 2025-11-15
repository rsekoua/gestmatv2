<?php

namespace App\Observers;

use App\Models\Attribution;
use App\Models\Materiel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
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

        // Vérifier que la date d'attribution est >= à la dernière date de restitution
        $lastRestitution = Attribution::where('materiel_id', $attribution->materiel_id)
            ->whereNotNull('date_restitution')
            ->orderBy('date_restitution', 'desc')
            ->first();

        if ($lastRestitution && $attribution->date_attribution < $lastRestitution->date_restitution) {
            throw ValidationException::withMessages([
                'date_attribution' => "La date d'attribution ({$attribution->date_attribution->format('d/m/Y')}) doit être égale ou postérieure à la dernière restitution de ce matériel ({$lastRestitution->date_restitution->format('d/m/Y')}).",
            ]);
        }

        // Mettre à jour le statut du matériel à "attribué"
        if ($attribution->materiel_id) {
            $updated = Materiel::withoutEvents(function () use ($attribution) {
                return Materiel::where('id', $attribution->materiel_id)
                    ->update(['statut' => 'attribué']);
            });

            // Vérifier que la mise à jour a bien fonctionné
            if ($updated === 0) {
                throw ValidationException::withMessages([
                    'materiel_id' => 'Erreur lors de la mise à jour du statut du matériel.',
                ]);
            }
        }
    }

    /**
     * Handle the Attribution "deleting" event.
     * Empêche la suppression des attributions restituées (historique à préserver).
     */
    public function deleting(Attribution $attribution): void
    {
        // Vérifier si l'attribution a été restituée
        if (! is_null($attribution->date_restitution)) {
            // Afficher une notification d'erreur
            Notification::make()
                ->danger()
                ->title('Suppression impossible')
                ->body('Impossible de supprimer une attribution restituée. L\'historique des restitutions doit être préservé pour la traçabilité et l\'audit.')
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'attribution' => 'Impossible de supprimer une attribution restituée. L\'historique des restitutions doit être préservé pour la traçabilité.',
            ]);
        }

        // Si on arrive ici, c'est une attribution active
        // La suppression est autorisée et le matériel sera remis à "disponible" dans deleted()
    }

    /**
     * Handle the Attribution "created" event.
     */
    public function created(Attribution $attribution): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Attribution "updating" event.
     */
    public function updating(Attribution $attribution): void
    {
        // Empêcher le changement de matériel qui modifie le type d'attribution
        if ($attribution->isDirty('materiel_id')) {
            $newMateriel = Materiel::with('materielType')->find($attribution->materiel_id);
            $isForEmployee = ! is_null($attribution->employee_id);
            $isForService = ! is_null($attribution->service_id);

            // Un ordinateur doit être attribué à un employé
            if ($newMateriel->materielType->isComputer() && ! $isForEmployee) {
                throw ValidationException::withMessages([
                    'materiel_id' => 'Un ordinateur ne peut être attribué qu\'à un employé. Cette attribution est faite à un service.',
                ]);
            }

            // Un non-ordinateur doit être attribué à un service
            if (! $newMateriel->materielType->isComputer() && ! $isForService) {
                throw ValidationException::withMessages([
                    'materiel_id' => 'Ce type de matériel ne peut être attribué qu\'à un service. Cette attribution est faite à un employé.',
                ]);
            }
        }

        // Empêcher le changement de destinataire (employé -> service ou service -> employé)
        if ($attribution->isDirty('employee_id') || $attribution->isDirty('service_id')) {
            $materiel = Materiel::with('materielType')->find($attribution->materiel_id);

            // Si on essaie d'attribuer à un service alors que c'est un ordinateur
            if ($attribution->isDirty('service_id') && ! is_null($attribution->service_id) && $materiel->materielType->isComputer()) {
                throw ValidationException::withMessages([
                    'service_id' => 'Un ordinateur ne peut être attribué qu\'à un employé, pas à un service.',
                ]);
            }

            // Si on essaie d'attribuer à un employé alors que c'est un non-ordinateur
            if ($attribution->isDirty('employee_id') && ! is_null($attribution->employee_id) && ! $materiel->materielType->isComputer()) {
                throw ValidationException::withMessages([
                    'employee_id' => 'Ce type de matériel ne peut être attribué qu\'à un service, pas à un employé.',
                ]);
            }
        }

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
        $this->clearCache();
    }

    /**
     * Handle the Attribution "deleted" event.
     * Note: Cette méthode n'est appelée que pour les attributions ACTIVES
     * car deleting() bloque la suppression des attributions restituées.
     */
    public function deleted(Attribution $attribution): void
    {
        // L'attribution supprimée était active (pas de date de restitution)
        // Remettre le matériel à "disponible"
        if (is_null($attribution->date_restitution) && $attribution->materiel_id) {
            Materiel::withoutEvents(function () use ($attribution) {
                return Materiel::where('id', $attribution->materiel_id)
                    ->update(['statut' => 'disponible']);
            });
        }

        $this->clearCache();
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

        $updated = Materiel::withoutEvents(function () use ($attribution, $newStatus) {
            return Materiel::where('id', $attribution->materiel_id)
                ->update(['statut' => $newStatus]);
        });

        // Vérifier que la mise à jour a bien fonctionné
        if ($updated === 0) {
            throw ValidationException::withMessages([
                'materiel_id' => 'Erreur lors de la mise à jour du statut du matériel à la restitution.',
            ]);
        }
    }

    /**
     * Clear all attribution-related caches
     */
    protected function clearCache(): void
    {
        Cache::forget('materiels.stats.widget');
        Cache::forget('dashboard.overview.stats');
        Cache::forget('dashboard.attributions.monthly');
        Cache::forget('navigation.badge.attributions');
        Cache::forget('navigation.badge.attributions.color');
        Cache::forget('navigation.badge.attributions.tooltip');
    }
}
