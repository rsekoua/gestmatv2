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
        return 50;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nom')
                ->label('Nom')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Dupont'),

            ImportColumn::make('prenom')
                ->label('Prénom')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Jean'),

            ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping()
                ->rules(['required', 'email', 'unique:employees,email', 'max:255'])
                ->example('jean.dupont@example.com'),

            ImportColumn::make('service_id')
                ->label('Service')
                ->requiredMapping()
                ->guess(['service', 'service_code', 'code_service'])
                ->castStateUsing(function (string $state): ?string {
                    // Try to find service by code first, then by name
                    $service = Service::where('code', $state)->first()
                        ?? Service::where('nom', $state)->first();

                    return $service?->id;
                })
                ->rules(['required', 'exists:services,id'])
                ->exampleHeader('service')
                ->example('DG'),

            ImportColumn::make('emploi')
                ->label('Emploi')
                ->rules(['nullable', 'max:255'])
                ->example('Ingénieur'),

            ImportColumn::make('fonction')
                ->label('Fonction')
                ->rules(['nullable', 'max:255'])
                ->example('Responsable Technique'),

            ImportColumn::make('telephone')
                ->label('Téléphone')
                ->rules(['nullable', 'max:255'])
                ->example('+33 1 23 45 67 89'),
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
