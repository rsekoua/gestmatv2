<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-home';

    protected  string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Tableau de Bord';

    protected static ?string $navigationLabel = 'Tableau de Bord';

    public function getWidgets(): array
    {
        return [
            // Ligne 1: Vue d'ensemble des statistiques (pleine largeur)
            \App\Filament\Widgets\DashboardOverviewWidget::class,

            // Ligne 2: Alertes (pleine largeur)
//            \App\Filament\Widgets\AlertsWidget::class,

            // Ligne 3: Graphiques côte à côte (6 colonnes chacun)
//            \App\Filament\Widgets\AttributionsChartWidget::class,
//            \App\Filament\Widgets\MaterielsStatusChartWidget::class,

            // Ligne 4: Graphique type matériels + Top Employés (6 colonnes chacun)
          //  \App\Filament\Widgets\MaterielsTypeChartWidget::class,
//            \App\Filament\Widgets\TopEmployeesWidget::class,

            // Ligne 5: Top Matériels seul (6 colonnes, aligné à gauche)
//            \App\Filament\Widgets\TopMaterielsWidget::class,

            // Ligne 6: Attributions récentes (pleine largeur)
//            \App\Filament\Widgets\RecentAttributionsWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 12,
            'sm' => 12,
            'md' => 12,
            'lg' => 12,
            'xl' => 12,
            '2xl' => 12,
        ];
    }
}
