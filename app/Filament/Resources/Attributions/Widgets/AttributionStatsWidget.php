<?php

namespace App\Filament\Resources\Attributions\Widgets;

use App\Models\Attribution;
use App\Models\Materiel;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttributionStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalAttributions = Attribution::count();
        $activeAttributions = Attribution::active()->count();
        $closedAttributions = Attribution::closed()->count();

        // Calculer la durée moyenne des attributions actives
        $avgDuration = Attribution::active()
            ->get()
            ->avg('duration_in_days');
        $avgDurationFormatted = $avgDuration ? round($avgDuration) : 0;

        // Matériel le plus attribué
        $topMateriel = Materiel::withCount('attributions')
            ->orderBy('attributions_count', 'desc')
            ->first();

        $topMaterielName = $topMateriel ? $topMateriel->nom : 'Aucun';
        $topMaterielCount = $topMateriel ? $topMateriel->attributions_count : 0;

        // Nombre d'attributions avec décision de rebut
        $rebutCount = Attribution::closed()
            ->where('decision_res', 'rebut')
            ->count();

        return [
            Stat::make('Total des Attributions', $totalAttributions)
                ->description('Toutes les attributions dans le système')
                ->descriptionIcon(Heroicon::ArrowsRightLeft)
                ->color('primary')
                ->chart($this->getAttributionsChartData()),

            Stat::make('Attributions Actives', $activeAttributions)
                ->description($closedAttributions.' attributions clôturées')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color($activeAttributions > 0 ? 'success' : 'gray'),

            Stat::make('Durée Moyenne', $avgDurationFormatted.' jours')
                ->description('Durée moyenne des attributions actives')
                ->descriptionIcon(Heroicon::Clock)
                ->color(match (true) {
                    $avgDurationFormatted < 30 => 'success',
                    $avgDurationFormatted < 180 => 'warning',
                    default => 'danger',
                }),

            Stat::make('Matériel Populaire', $topMaterielCount.' fois')
                ->description($topMaterielName)
                ->descriptionIcon(Heroicon::ComputerDesktop)
                ->color('info'),

            Stat::make('Rebuts', $rebutCount)
                ->description('Attributions avec décision de rebut')
                ->descriptionIcon(Heroicon::ExclamationTriangle)
                ->color($rebutCount > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getAttributionsChartData(): array
    {
        // Génère un graphique simple des 7 derniers mois
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = Attribution::whereYear('date_attribution', $date->year)
                ->whereMonth('date_attribution', $date->month)
                ->count();
        }

        return $data;
    }
}
