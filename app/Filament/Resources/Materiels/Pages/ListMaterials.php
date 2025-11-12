<?php

namespace App\Filament\Resources\Materiels\Pages;

use App\Filament\Resources\Materiels\MaterialResource;
use App\Filament\Resources\Materiels\Widgets\MaterialStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterials extends ListRecords
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')->label(''),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MaterialStatsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 4,
        ];
    }
}
