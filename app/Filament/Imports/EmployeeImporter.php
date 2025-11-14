<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use App\Models\Service;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    /**
     * Optimize import performance by processing in chunks
     */
    public function getChunkSize(): int
    {
        return 500;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nom')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('DUPONT'),

            ImportColumn::make('prenom')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Jean'),

            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'unique:employees,email'])
                ->example('jean.dupont@mshpcmu.cd'),

            ImportColumn::make('service')
                ->label('Service Code')
                ->requiredMapping()
                ->relationship(resolveUsing: function (string $state): ?Service {
                    return Service::where('code', $state)->first();
                })
                ->rules(['required'])
                ->exampleHeader('service_code')
                ->example('DSI'),

            ImportColumn::make('telephone')
                ->rules(['nullable', 'max:255'])
                ->example('+243 123 456 789'),

            ImportColumn::make('emploi')
                ->rules(['nullable', 'max:255'])
                ->example('CDI'),

            ImportColumn::make('fonction')
                ->rules(['nullable', 'max:255'])
                ->example('Développeur'),
        ];
    }

    public function resolveRecord(): ?Employee
    {
        return Employee::firstOrNew([
            'email' => $this->data['email'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import des employés terminé. '.number_format($import->successful_rows).' employé(s) importé(s) avec succès.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' employé(s) en échec.';
        }

        return $body;
    }
}
