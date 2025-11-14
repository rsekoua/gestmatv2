<?php

namespace App\Filament\Resources\Attributions\Pages;

use App\Filament\Resources\Attributions\AttributionResource;
use App\Filament\Resources\Attributions\Widgets\AttributionStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListAttributions extends ListRecords
{
    protected static string $resource = AttributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus)
                ->color('primary')
                ->label('Nouvelle attribution')
            ->size('sm'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
           // AttributionStatsWidget::class,
        ];
    }
}
