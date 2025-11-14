<?php

namespace App\Filament\Widgets;

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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

        return [
            Stat::make('Matériels Totaux', $totalMateriels)
                ->description("{$disponibles} disponibles · {$attribues} attribués · {$enPanne} en panne")
                ->descriptionIcon('heroicon-o-computer-desktop')
                ->color('primary')
                ->chart($this->getMaterielsMonthlyData()),

            Stat::make('Taux de Disponibilité', "{$disponibiliteRate}%")
                ->description($disponibiliteRate >= 50 ? 'Bon niveau de stock' : 'Stock faible')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($disponibiliteRate >= 50 ? 'success' : ($disponibiliteRate >= 25 ? 'warning' : 'danger')),

            Stat::make('Attributions Actives', $activeAttributions)
                ->description("{$closedThisMonth} clôturées ce mois")
                ->descriptionIcon('heroicon-o-arrows-right-left')
                ->color('info')
                ->chart($this->getAttributionsMonthlyData()),

            Stat::make('Employés Équipés', "{$employeesRate}%")
                ->description("{$employeesWithActiveAttributions} / {$totalEmployees} employés")
                ->descriptionIcon('heroicon-o-users')
                ->color($employeesRate >= 50 ? 'success' : 'warning'),

            Stat::make('Services', $totalServices)
                ->description('Services actifs')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('gray'),
        ];
    }

    protected function getMaterielsMonthlyData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = Materiel::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return $data;
    }

    protected function getAttributionsMonthlyData(): array
    {
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
