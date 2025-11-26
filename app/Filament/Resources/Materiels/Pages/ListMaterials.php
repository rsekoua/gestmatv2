<?php

namespace App\Filament\Resources\Materiels\Pages;

use App\Filament\Exports\MaterielExporter;
use App\Filament\Imports\MaterielImporter;
use App\Filament\Resources\Materiels\MaterialResource;
use App\Filament\Resources\Materiels\Widgets\MaterialStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterials extends ListRecords
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            ExportAction::make()
//                ->exporter(MaterielExporter::class)
//                ->label('Exporter')
//                ->color('primary')
//                ->size('sm')
//                ->icon('heroicon-o-arrow-up-tray')
//                ->columnMapping(true)
////                ->enableVisibleTableColumnsByDefault()
//                ->columnMappingColumns(2)
//                ->fileName(fn (): string => 'materiels-'.now()->format('Y-m-d-His')),
//            ImportAction::make()
//                ->importer(MaterielImporter::class)
//                ->label('Importer')
//                ->size('sm')
//                ->color('success')
//                ->icon('heroicon-o-arrow-down-tray'),
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Nouveau material')
                ->size('sm'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
          //  MaterialStatsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 6;
    }
}
