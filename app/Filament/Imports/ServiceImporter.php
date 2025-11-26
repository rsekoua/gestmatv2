<?php

namespace App\Filament\Imports;

use App\Models\Service;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ServiceImporter extends Importer
{
    protected static ?string $model = Service::class;

    /**
     * Optimize import performance by processing in chunks
     */
    public function getChunkSize(): int
    {
        return 50;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nom')
                ->label('Nom du service')
                ->requiredMapping()
                ->rules(['required', 'unique:services,nom', 'max:255'])
                ->example('Direction Générale'),

            ImportColumn::make('code')
                ->label('Code du service')
                ->requiredMapping()
                ->rules(['required', 'unique:services,code', 'max:255'])
                ->example('DG'),

            ImportColumn::make('responsable')
                ->label('Responsable')
                ->rules(['nullable', 'max:255'])
                ->example('Jean Dupont'),
        ];
    }

    public function resolveRecord(): ?Service
    {
        return Service::firstOrNew([
            'code' => $this->data['code'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import des services terminé. '.number_format($import->successful_rows).' service(s) importé(s) avec succès.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' service(s) en échec.';
        }

        return $body;
    }
}
