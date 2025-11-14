<?php

namespace App\Filament\Resources\Materiels\Widgets;

use App\Models\Materiel;
use App\Models\MaterielType;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class MaterialStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Cache for 5 minutes to improve performance
        $stats = Cache::remember('materiels.stats.widget', now()->addMinutes(5), function () {
            $totalMateriels = Materiel::count();
            $disponibles = Materiel::where('statut', 'disponible')->count();
            $attribues = Materiel::where('statut', 'attribué')->count();
            $enPanne = Materiel::where('statut', 'en_panne')->count();
            $enMaintenance = Materiel::where('statut', 'en_maintenance')->count();
            $rebutes = Materiel::where('statut', 'rebuté')->count();

            // Matériels amortis (ordinateurs de + de 3 ans)
            $amortis = Materiel::depreciated()->count();

            // Type de matériel le plus représenté
            $topType = MaterielType::withCount('materiels')
                ->orderBy('materiels_count', 'desc')
                ->first();

            $topTypeName = $topType ? $topType->nom : 'Aucun';
            $topTypeCount = $topType ? $topType->materiels_count : 0;

            // Calcul du pourcentage de disponibilité
            $disponibilitePercentage = $totalMateriels > 0
                ? round(($disponibles / $totalMateriels) * 100)
                : 0;

            return compact(
                'totalMateriels',
                'disponibles',
                'attribues',
                'enPanne',
                'enMaintenance',
                'rebutes',
                'amortis',
                'topTypeName',
                'topTypeCount',
                'disponibilitePercentage'
            );
        });

        extract($stats);

        return [
            Stat::make('Total des Matériels', $totalMateriels)
                ->description("{$rebutes} rebutés | {$enPanne} en panne | {$enMaintenance} en maintenance")
                ->descriptionIcon(Heroicon::ComputerDesktop)
                ->color('primary')
                ->chart($this->getMaterielsChartData()),

            Stat::make('Disponibilité', "{$disponibilitePercentage}%")
                ->description("{$disponibles} disponibles | {$attribues} attribués")
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color($disponibilitePercentage >= 50 ? 'success' : ($disponibilitePercentage >= 25 ? 'warning' : 'danger')),

            Stat::make('Matériels Amortis', $amortis)
                ->description('Ordinateurs de plus de 3 ans')
                ->descriptionIcon(Heroicon::ExclamationTriangle)
                ->color($amortis > 0 ? 'danger' : 'success'),

            Stat::make('Type Principal', $topTypeCount)
                ->description($topTypeName)
                ->descriptionIcon(Heroicon::Tag)
                ->color('info'),
            Stat::make('Matériels Amortis', $amortis)
                ->description('Ordinateurs de plus de 3 ans')
                ->descriptionIcon(Heroicon::ExclamationTriangle)
                ->color($amortis > 0 ? 'danger' : 'success'),

            Stat::make('Type Principal', $topTypeCount)
                ->description($topTypeName)
                ->descriptionIcon(Heroicon::Tag)
                ->color('info'),
        ];
    }

    protected function getMaterielsChartData(): array
    {
        return Cache::remember('materiels.chart.data', now()->addHour(), function () {
            // Génère un graphique simple des 7 derniers mois
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $data[] = Materiel::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
            }

            return $data;
        });
    }
}
