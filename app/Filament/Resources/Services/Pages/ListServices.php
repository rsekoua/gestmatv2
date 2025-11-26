<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Imports\ServiceImporter;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Services\Widgets\ServiceStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(ServiceImporter::class)
                ->label('Importer')
                ->size('sm')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->job(null), // Process synchronously to show progress immediately
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Nouveau service')
                ->size('sm'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // ServiceStatsWidget::class,
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
