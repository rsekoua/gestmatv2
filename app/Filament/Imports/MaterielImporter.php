<?php

namespace App\Filament\Imports;

use App\Models\Materiel;
use App\Models\MaterielType;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MaterielImporter extends Importer
{
    protected static ?string $model = Materiel::class;

    /**
     * Optimize import performance by processing in chunks
     */
    public function getChunkSize(): int
    {
        return 50;
    }

    /**
     * @throws \Exception
     */
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('numero_serie')
                ->requiredMapping()
                ->rules(['required', 'unique:materiels,numero_serie', 'max:255'])
                ->example('SN001'),

            ImportColumn::make('materiel_type_id')
                ->label('Type de Matériel')
                ->requiredMapping()
                ->guess(['type_materiel', 'type', 'materiel_type'])
                ->castStateUsing(function (string $state): ?string {
                    $type = MaterielType::where('nom', $state)->first();

                    return $type?->id;
                })
                ->rules(['required', 'exists:materiel_types,id'])
                ->exampleHeader('type_materiel')
                ->example('Ordinateur Portable'),

            ImportColumn::make('marque')
                ->rules(['nullable', 'max:255'])
                ->example('Dell'),

            ImportColumn::make('modele')
                ->rules(['nullable', 'max:255'])
                ->example('Latitude 5420'),

            ImportColumn::make('statut')
                ->requiredMapping()
                ->rules(['required', 'in:disponible,attribué,en_panne,en_maintenance,rebuté'])
                ->example('disponible'),

            ImportColumn::make('etat_physique')
                ->rules(['nullable', 'in:excellent,bon,moyen,mauvais'])
                ->example('bon'),

            ImportColumn::make('purchase_date')
                ->label('Date d\'achat')
                ->rules(['nullable', 'date'])
                ->castStateUsing(function ($state) {
                    if (empty($state)) {
                        return null;
                    }

                    // Try to parse different date formats
                    try {
                        // Handle formats like "01/01/2022" or "01-01-2022"
                        if (preg_match('/^(\d{2})[\/-](\d{2})[\/-](\d{4})$/', $state, $matches)) {
                            return sprintf('%s-%s-%s', $matches[3], $matches[2], $matches[1]);
                        }

                        // Already in correct format or parseable by Carbon
                        return $state;
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->exampleHeader('purchase_date')
                ->example('2023-01-15'),

            ImportColumn::make('acquision')
                ->label('Mode d\'acquisition')
                ->rules(['nullable', 'max:255'])
                ->example('Achat'),

            ImportColumn::make('processor')
                ->label('Processeur')
                ->rules(['nullable', 'max:255'])
                ->example('Intel Core i5-1135G7'),

            ImportColumn::make('ram_size_gb')
                ->label('RAM (GB)')
                ->rules(['nullable', 'integer', 'min:1'])
                ->castStateUsing(function ($state) {
                    if (empty($state)) {
                        return null;
                    }
                    // Remove "Go", "GB", spaces and convert to integer
                    $cleaned = preg_replace('/[^\d]/', '', $state);

                    return $cleaned ? (int) $cleaned : null;
                })
                ->example('16'),

            ImportColumn::make('storage_size_gb')
                ->label('Stockage (GB)')
                ->rules(['nullable', 'integer', 'min:1'])
                ->castStateUsing(function ($state) {
                    if (empty($state)) {
                        return null;
                    }
                    // Remove "Go", "GB", "To", "TB", spaces, commas and convert to integer
                    // Handle "1 To" -> 1000 GB, "2,5 To" -> 2500 GB
                    $state = str_replace(',', '.', $state);

                    if (stripos($state, 'To') !== false || stripos($state, 'TB') !== false) {
                        $value = (float) preg_replace('/[^\d.]/', '', $state);

                        return $value ? (int) ($value * 1000) : null;
                    }

                    $cleaned = preg_replace('/[^\d]/', '', $state);

                    return $cleaned ? (int) $cleaned : null;
                })
                ->example('512'),

            ImportColumn::make('screen_size')
                ->label('Taille écran (pouces)')
                ->rules(['nullable', 'numeric', 'min:0'])
                ->castStateUsing(function ($state) {
                    return $state ? (float) $state : null;
                })
                ->example('15.6'),

            ImportColumn::make('notes')
                ->rules(['nullable'])
                ->example('Garantie jusqu\'au 01/2026'),
        ];
    }

    public function resolveRecord(): ?Materiel
    {
        return Materiel::firstOrNew([
            'numero_serie' => $this->data['numero_serie'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import du matériel terminé. '.number_format($import->successful_rows).' matériel(s) importé(s) avec succès.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' matériel(s) en échec.';
        }

        return $body;
    }
}
