<?php

namespace App\Filament\Exports;

use App\Models\Materiel;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MaterielExporter extends Exporter
{
    protected static ?string $model = Materiel::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID')
                ->enabledByDefault(false),

            ExportColumn::make('materielType.nom')
                ->label('Type de Matériel'),

            ExportColumn::make('marque')
                ->label('Marque'),

            ExportColumn::make('modele')
                ->label('Modèle'),

            ExportColumn::make('numero_serie')
                ->label('Numéro de Série'),

            ExportColumn::make('processor')
                ->label('Processeur')
                ->enabledByDefault(false),

            ExportColumn::make('ram_size_gb')
                ->label('RAM (GB)')
                ->enabledByDefault(false),

            ExportColumn::make('storage_size_gb')
                ->label('Stockage (GB)')
                ->enabledByDefault(false),

            ExportColumn::make('screen_size')
                ->label('Taille Écran (pouces)')
                ->enabledByDefault(false),

            ExportColumn::make('purchase_date')
                ->label('Date d\'Achat'),

            ExportColumn::make('acquision')
                ->label('Mode d\'Acquisition'),

            ExportColumn::make('statut')
                ->label('Statut')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'en_panne' => 'En panne',
                    'en_maintenance' => 'En maintenance',
                    'attribué' => 'Attribué',
                    'disponible' => 'Disponible',
                    'rebuté' => 'Rebuté',
                    default => ucfirst($state),
                }),

            ExportColumn::make('etat_physique')
                ->label('État Physique')
                ->formatStateUsing(fn (string $state): string => ucfirst($state)),

            ExportColumn::make('amortissement_status')
                ->label('Amortissement')
                ->state(fn (Materiel $record): string => $record->amortissement_status)
                ->enabledByDefault(false),

            ExportColumn::make('specifications_summary')
                ->label('Spécifications')
                ->state(fn (Materiel $record): string => $record->specifications_summary ?? '')
                ->enabledByDefault(false),

            ExportColumn::make('attributions_count')
                ->label('Nombre d\'Attributions')
                ->counts('attributions')
                ->enabledByDefault(false),

            ExportColumn::make('active_attribution.employee.full_name')
                ->label('Employé Actuel'),

            ExportColumn::make('active_attribution.service.nom')
                ->label('Service Actuel'),

            ExportColumn::make('active_attribution.date_attribution')
                ->label('Date Attribution Actuelle'),

            ExportColumn::make('notes')
                ->label('Notes')
                ->enabledByDefault(false),

            ExportColumn::make('created_at')
                ->label('Créé le')
                ->enabledByDefault(false),

            ExportColumn::make('updated_at')
                ->label('Modifié le')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'L\'export de '.number_format($export->successful_rows).' '.str('matériel')->plural($export->successful_rows).' est terminé.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('ligne')->plural($failedRowsCount).' a échoué.';
        }

        return $body;
    }

    public function getJobBatchName(): ?string
    {
        return 'export-materiels';
    }
}
