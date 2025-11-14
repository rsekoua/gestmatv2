<?php

namespace App\Filament\Widgets;

use App\Models\Attribution;
use App\Models\Materiel;
use Filament\Widgets\Widget;

class AlertsWidget extends Widget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'default' => 12,
        'sm' => 12,
        'md' => 12,
        'lg' => 12,
        'xl' => 12,
        '2xl' => 12,
    ];

    protected  string $view = 'filament.widgets.alerts-widget';

    protected static ?string $pollingInterval = '60s';

    public function getAlerts(): array
    {
        $alerts = [];

        // Matériels en panne
        $enPanne = Materiel::where('statut', 'en_panne')->count();
        if ($enPanne > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => "Matériels en panne",
                'message' => "{$enPanne} matériel(s) nécessite(nt) une réparation.",
                'icon' => 'heroicon-o-exclamation-triangle',
                'action' => 'Voir les matériels',
                'url' => route('filament.admin.resources.materiels.materials.index', ['tableFilters[statut][value]' => 'en_panne']),
            ];
        }

        // Matériels amortis
        $amortis = Materiel::depreciated()->count();
        if ($amortis > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => "Matériels amortis",
                'message' => "{$amortis} ordinateur(s) ont plus de 3 ans et pourraient nécessiter un remplacement.",
                'icon' => 'heroicon-o-information-circle',
                'action' => 'Voir les ordinateurs',
                'url' => route('filament.admin.resources.materiels.materials.index'),
            ];
        }

        // Attributions longue durée (plus de 1 an)
        $longuesDurees = Attribution::active()
            ->get()
            ->filter(fn ($attr) => $attr->duration_in_days > 365)
            ->count();
        if ($longuesDurees > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => "Attributions de longue durée",
                'message' => "{$longuesDurees} attribution(s) sont active(s) depuis plus d'un an.",
                'icon' => 'heroicon-o-clock',
                'action' => 'Voir les attributions',
              //  'url' => route('filament.admin.resources.attributions.materials.index'),
            ];
        }

        // Stock faible (moins de 20% disponible)
        $totalMateriels = Materiel::count();
        $disponibles = Materiel::where('statut', 'disponible')->count();
        $tauxDisponibilite = $totalMateriels > 0 ? ($disponibles / $totalMateriels) * 100 : 0;

        if ($tauxDisponibilite < 20 && $totalMateriels > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => "Stock critique",
                'message' => "Seulement {$disponibles} matériel(s) disponible(s) sur {$totalMateriels} ({$tauxDisponibilite}%).",
                'icon' => 'heroicon-o-exclamation-circle',
                'action' => 'Voir le stock',
                'url' => route('filament.admin.resources.materiels.materials.index', ['tableFilters[statut][value]' => 'disponible']),
            ];
        }

        // Si aucune alerte
        if (empty($alerts)) {
            $alerts[] = [
                'type' => 'success',
                'title' => "Système opérationnel",
                'message' => "Aucune alerte à signaler. Tout fonctionne correctement.",
                'icon' => 'heroicon-o-check-circle',
                'action' => null,
                'url' => null,
            ];
        }

        return $alerts;
    }
}
