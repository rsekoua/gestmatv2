<?php

namespace App\Filament\Resources\Services\Widgets;

use App\Models\Service;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServiceStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalServices = Service::count();
        $servicesWithResponsable = Service::whereNotNull('responsable')->count();
        $servicesWithEmployees = Service::has('employees')->count();
        $servicesWithoutEmployees = $totalServices - $servicesWithEmployees;
        $totalEmployees = \App\Models\Employee::count();
        $avgEmployeesPerService = $servicesWithEmployees > 0
            ? round($totalEmployees / $servicesWithEmployees, 1)
            : 0;

        return [
            Stat::make('Total des Services', $totalServices)
                ->description('Services enregistrés dans le système')
                ->descriptionIcon(Heroicon::BuildingOffice2)
                ->color('primary')
                ->chart($this->getServicesChartData()),

            Stat::make('Services avec Responsable', $servicesWithResponsable)
                ->description(($totalServices > 0 ? round(($servicesWithResponsable / $totalServices) * 100) : 0) . '% des services')
                ->descriptionIcon(Heroicon::User)
                ->color('success'),

            Stat::make('Services avec Employés', $servicesWithEmployees)
                ->description($servicesWithoutEmployees . ' services sans employés')
                ->descriptionIcon(Heroicon::Users)
                ->color('warning'),

            Stat::make('Moyenne Employés/Service', $avgEmployeesPerService)
                ->description('Répartition moyenne des employés')
                ->descriptionIcon(Heroicon::ChartBar)
                ->color('info'),
        ];
    }

    protected function getServicesChartData(): array
    {
        // Génère un graphique simple des 7 derniers mois
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = Service::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return $data;
    }
}
