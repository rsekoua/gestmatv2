<?php

namespace App\Filament\Widgets;

use App\Models\Materiel;
use Filament\Widgets\ChartWidget;

class MaterielsStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Répartition des Matériels par Statut';

    protected static ?int $sort = 4;

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
        $disponibles = Materiel::where('statut', 'disponible')->count();
        $attribues = Materiel::where('statut', 'attribué')->count();
        $enPanne = Materiel::where('statut', 'en_panne')->count();
        $enMaintenance = Materiel::where('statut', 'en_maintenance')->count();
        $rebutes = Materiel::where('statut', 'rebuté')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Matériels',
                    'data' => [$disponibles, $attribues, $enPanne, $enMaintenance, $rebutes],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.7)',  // vert - disponible
                        'rgba(59, 130, 246, 0.7)',  // bleu - attribué
                        'rgba(249, 115, 22, 0.7)',  // orange - en panne
                        'rgba(234, 179, 8, 0.7)',   // jaune - en maintenance
                        'rgba(239, 68, 68, 0.7)',   // rouge - rebuté
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(249, 115, 22)',
                        'rgb(234, 179, 8)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Disponibles', 'Attribués', 'En Panne', 'En Maintenance', 'Rebutés'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
