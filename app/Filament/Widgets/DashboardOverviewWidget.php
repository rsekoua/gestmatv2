<?php

namespace App\Filament\Widgets;

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\Service;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class DashboardOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = [
        'default' => 12,
        'sm' => 12,
        'md' => 12,
        'lg' => 12,
        'xl' => 12,
        '2xl' => 12,
    ];

    protected function getStats(): array
    {
        // Cache for 5 minutes to improve dashboard performance
        $stats = Cache::remember('dashboard.overview.stats', now()->addMinutes(5), function () {
            // Matériels
            $totalMateriels = Materiel::count();
            $disponibles = Materiel::where('statut', 'disponible')->count();
            $attribues = Materiel::where('statut', 'attribué')->count();
            $enPanne = Materiel::where('statut', 'en_panne')->count();
            $disponibiliteRate = $totalMateriels > 0 ? round(($disponibles / $totalMateriels) * 100) : 0;

            // Attributions
            $activeAttributions = Attribution::active()->count();
            $closedThisMonth = Attribution::closed()
                ->whereMonth('date_restitution', now()->month)
                ->whereYear('date_restitution', now()->year)
                ->count();

            // Employés
            $totalEmployees = Employee::count();
            $employeesWithActiveAttributions = Employee::has('activeAttributions')->count();
            $employeesRate = $totalEmployees > 0 ? round(($employeesWithActiveAttributions / $totalEmployees) * 100) : 0;

            // Services
            $totalServices = Service::count();

            return compact(
                'totalMateriels',
                'disponibles',
                'attribues',
                'enPanne',
                'disponibiliteRate',
                'activeAttributions',
                'closedThisMonth',
                'totalEmployees',
                'employeesWithActiveAttributions',
                'employeesRate',
                'totalServices'
            );
        });

        extract($stats);

        return [
            Stat::make('Matériels Totaux', $totalMateriels)
                ->description("{$disponibles} disponibles · {$attribues} attribués · {$enPanne} en panne")
                ->descriptionIcon(Heroicon::ComputerDesktop)
                ->color('primary')
                ->chart($this->getMaterielsMonthlyData()),

//            Stat::make('Taux de Disponibilité', "{$disponibiliteRate}%")
//                ->description($disponibiliteRate >= 50 ? 'Bon niveau de stock' : 'Stock faible')
//                ->descriptionIcon(Heroicon::CheckCircle)
//                ->color($disponibiliteRate >= 50 ? 'success' : ($disponibiliteRate >= 25 ? 'warning' : 'danger')),

            Stat::make('Attributions Actives', $activeAttributions)
                ->description("{$closedThisMonth} clôturées ce mois")
                ->descriptionIcon(Heroicon::ArrowsRightLeft)
                ->color('info')
                ->chart($this->getAttributionsMonthlyData()),

//            Stat::make('Employés Équipés', "{$employeesRate}%")
//                ->description("{$employeesWithActiveAttributions} / {$totalEmployees} employés")
//                ->descriptionIcon(Heroicon::Users)
//                ->color($employeesRate >= 50 ? 'success' : 'warning'),

            Stat::make('Services', $totalServices)
                ->description('Services actifs')
                ->descriptionIcon(Heroicon::BuildingOffice2)
                ->color('gray'),
        ];
    }

    protected function getMaterielsMonthlyData(): array
    {
        return Cache::remember('dashboard.materiels.monthly', now()->addHour(), function () {
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

    protected function getAttributionsMonthlyData(): array
    {
        return Cache::remember('dashboard.attributions.monthly', now()->addHour(), function () {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $data[] = Attribution::whereYear('date_attribution', $date->year)
                    ->whereMonth('date_attribution', $date->month)
                    ->count();
            }

            return $data;
        });
    }
}
