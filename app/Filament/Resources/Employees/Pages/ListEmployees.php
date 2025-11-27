<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Imports\EmployeeImporter;
use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\Employees\Widgets\EmployeeStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(EmployeeImporter::class)
                ->label('Importer')
                ->size('sm')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->job(null), // Process synchronously to show progress immediately
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Nouvel employé')
                ->size('sm'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // EmployeeStatsWidget::class,
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
