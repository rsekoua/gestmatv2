<?php

namespace App\Filament\Widgets;

use App\Models\MaterielType;
use Filament\Widgets\ChartWidget;

class MaterielsTypeChartWidget extends ChartWidget
{
    protected ?string $heading = 'Répartition par Type de Matériel';

    protected static ?int $sort = 5;

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
        $types = MaterielType::has('materiels')
            ->withCount('materiels')
            ->orderBy('materiels_count', 'desc')
            ->get();

        $labels = $types->pluck('nom')->toArray();
        $data = $types->pluck('materiels_count')->toArray();

        // Génération de couleurs dynamiques
        $colors = $this->generateColors(count($labels));

        return [
            'datasets' => [
                [
                    'label' => 'Nombre de matériels',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function generateColors(int $count): array
    {
        $baseColors = [
            'rgba(59, 130, 246, 0.7)',   // bleu
            'rgba(34, 197, 94, 0.7)',    // vert
            'rgba(249, 115, 22, 0.7)',   // orange
            'rgba(168, 85, 247, 0.7)',   // violet
            'rgba(236, 72, 153, 0.7)',   // rose
            'rgba(234, 179, 8, 0.7)',    // jaune
            'rgba(20, 184, 166, 0.7)',   // teal
            'rgba(239, 68, 68, 0.7)',    // rouge
        ];

        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }

        return $colors;
    }
}
