<?php

namespace App\Filament\Concerns;

use App\Models\Attribution;
use App\Models\Materiel;
use Filament\Schemas\Components\Utilities\Get;

trait ValidatesAttributionDates
{
    /**
     * Get the minimum date for attribution based on purchase date and last restitution.
     */
    protected function getMinimumAttributionDate(Materiel $materiel, ?Attribution $currentAttribution = null): ?\Carbon\Carbon
    {
        // Chercher la dernière restitution de ce matériel
        $query = Attribution::where('materiel_id', $materiel->id)
            ->whereNotNull('date_restitution');

        // En modification, exclure cette attribution et les suivantes
        if ($currentAttribution) {
            $query->where('date_attribution', '<', $currentAttribution->date_attribution);
        }

        $lastRestitution = $query->orderBy('date_restitution', 'desc')->first();

        // La date minimale est la plus récente entre la date d'achat et la dernière restitution
        $minDateFromRestitution = $lastRestitution?->date_restitution;
        $minDateFromPurchase = $materiel->purchase_date;

        if ($minDateFromRestitution && $minDateFromPurchase) {
            return $minDateFromRestitution->gt($minDateFromPurchase)
                ? $minDateFromRestitution
                : $minDateFromPurchase;
        }

        return $minDateFromRestitution ?? $minDateFromPurchase;
    }

    /**
     * Get helper text for attribution date field.
     */
    protected function getAttributionDateHelperText(Materiel $materiel, ?Attribution $currentAttribution = null): string
    {
        // Chercher la dernière restitution de ce matériel
        $query = Attribution::where('materiel_id', $materiel->id)
            ->whereNotNull('date_restitution');

        // En modification, exclure cette attribution et les suivantes
        if ($currentAttribution) {
            $query->where('date_attribution', '<', $currentAttribution->date_attribution);
        }

        $lastRestitution = $query->orderBy('date_restitution', 'desc')->first();

        $messages = [];

        if ($materiel->purchase_date) {
            $messages[] = "📦 Date d'achat : {$materiel->purchase_date->format('d/m/Y')}";
        }

        if ($lastRestitution) {
            $messages[] = "🔄 Dernière restitution : {$lastRestitution->date_restitution->format('d/m/Y')}";
        }

        if (empty($messages)) {
            return 'Première attribution de ce matériel';
        }

        return implode(' | ', $messages).' - La date d\'attribution doit être égale ou postérieure.';
    }

    /**
     * Get validation messages for attribution date field.
     */
    protected function getAttributionDateValidationMessages(): array
    {
        return [
            'after_or_equal' => 'La date d\'attribution doit être égale ou postérieure à la date d\'achat du matériel et à sa dernière restitution.',
        ];
    }

    /**
     * Get minimum date closure for use in forms (with Get parameter).
     */
    protected function getMinDateClosure(): \Closure
    {
        return function (Get $get, $record) {
            $materielId = $get('materiel_id');

            if (! $materielId) {
                return null;
            }

            $materiel = Materiel::find($materielId);

            if (! $materiel) {
                return null;
            }

            return $this->getMinimumAttributionDate($materiel, $record);
        };
    }

    /**
     * Get helper text closure for use in forms (with Get parameter).
     */
    protected function getHelperTextClosure(): \Closure
    {
        return function (Get $get, $record) {
            $materielId = $get('materiel_id');

            if (! $materielId) {
                return 'Sélectionnez d\'abord un matériel';
            }

            $materiel = Materiel::find($materielId);

            if (! $materiel) {
                return 'Matériel introuvable';
            }

            return $this->getAttributionDateHelperText($materiel, $record);
        };
    }
}
