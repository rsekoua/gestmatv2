<?php

namespace App\Filament\Resources\Employees\Widgets;

use App\Models\Employee;
use App\Models\Service;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalEmployees = Employee::count();
        $employeesWithService = Employee::whereNotNull('service_id')->count();
        $employeesWithoutService = $totalEmployees - $employeesWithService;
        $employeesWithAttributions = Employee::has('attributions')->count();
        $employeesWithActiveAttributions = Employee::has('activeAttributions')->count();

        // Service le plus peuplé
        $topService = Service::withCount('employees')
            ->orderBy('employees_count', 'desc')
            ->first();

        $topServiceName = $topService ? $topService->nom : 'Aucun';
        $topServiceCount = $topService ? $topService->employees_count : 0;

        return [
            Stat::make('Total des Employés', $totalEmployees)
                ->description('Employés enregistrés dans le système')
                ->descriptionIcon(Heroicon::Users)
                ->color('primary')
                ->chart($this->getEmployeesChartData()),

            Stat::make('Employés Assignés', $employeesWithService)
                ->description($employeesWithoutService . ' employés non assignés')
                ->descriptionIcon(Heroicon::BuildingOffice2)
                ->color($employeesWithoutService > 0 ? 'warning' : 'success'),

            Stat::make('Avec Attributions', $employeesWithAttributions)
                ->description($employeesWithActiveAttributions . ' avec attributions actives')
                ->descriptionIcon(Heroicon::Cube)
                ->color('info'),

            Stat::make('Service Principal', $topServiceCount)
                ->description($topServiceName)
                ->descriptionIcon(Heroicon::ChartBar)
                ->color('success'),
        ];
    }

    protected function getEmployeesChartData(): array
    {
        // Génère un graphique simple des 7 derniers mois
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = Employee::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return $data;
    }
}
