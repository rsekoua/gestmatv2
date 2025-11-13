<?php

namespace App\Filament\Widgets;

use App\Models\Attribution;
use Filament\Widgets\ChartWidget;

class AttributionsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Évolution des Attributions';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'default' => 12,
        'sm' => 12,
        'md' => 12,
        'lg' => 6,
        'xl' => 6,
        '2xl' => 6,
    ];

    protected  ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $months = [];
        $newAttributions = [];
        $restitutions = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $newAttributions[] = Attribution::whereYear('date_attribution', $date->year)
                ->whereMonth('date_attribution', $date->month)
                ->count();

            $restitutions[] = Attribution::whereYear('date_restitution', $date->year)
                ->whereMonth('date_restitution', $date->month)
                ->whereNotNull('date_restitution')
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nouvelles Attributions',
                    'data' => $newAttributions,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Restitutions',
                    'data' => $restitutions,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
