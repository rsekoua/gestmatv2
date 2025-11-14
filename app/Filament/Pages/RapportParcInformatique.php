<?php

namespace App\Filament\Pages;

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\MaterielType;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class RapportParcInformatique extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'filament.pages.rapport-parc-informatique';

    protected static ?string $navigationLabel = 'Rapport du Parc';

    protected static ?string $title = 'Rapport de l\'État du Parc Informatique';

    protected static ?string $navigationGroup = 'Rapports';

    protected static ?int $navigationSort = 1;

    public ?string $dateReference = null;

    public ?string $materielTypeId = null;

    public ?string $serviceId = null;

    public ?string $statutFiltre = null;

    public function mount(): void
    {
        $this->form->fill([
            'dateReference' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filtres du Rapport')
                    ->description('Sélectionnez les critères pour générer le rapport')
                    ->schema([
                        DatePicker::make('dateReference')
                            ->label('Date de référence')
                            ->default(now())
                            ->native(false)
                            ->maxDate(now())
                            ->required()
                            ->helperText('Le rapport montrera l\'état du parc à cette date'),

                        Select::make('materielTypeId')
                            ->label('Type de matériel')
                            ->options(MaterielType::pluck('nom', 'id'))
                            ->placeholder('Tous les types')
                            ->searchable(),

                        Select::make('serviceId')
                            ->label('Service')
                            ->options(Service::pluck('nom', 'id'))
                            ->placeholder('Tous les services')
                            ->searchable(),

                        Select::make('statutFiltre')
                            ->label('Statut')
                            ->options([
                                'disponible' => 'Disponible',
                                'attribué' => 'Attribué',
                                'en_panne' => 'En Panne',
                                'en_maintenance' => 'En Maintenance',
                                'rebuté' => 'Rebuté',
                            ])
                            ->placeholder('Tous les statuts')
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getFormData(): array
    {
        return $this->form->getState();
    }

    public function getStatistiquesGlobales(): array
    {
        $data = $this->getFormData();
        $query = Materiel::query();

        if (!empty($data['materielTypeId'])) {
            $query->where('materiel_type_id', $data['materielTypeId']);
        }

        if (!empty($data['statutFiltre'])) {
            $query->where('statut', $data['statutFiltre']);
        }

        $totalMateriels = $query->count();
        $disponibles = (clone $query)->where('statut', 'disponible')->count();
        $attribues = (clone $query)->where('statut', 'attribué')->count();
        $enPanne = (clone $query)->where('statut', 'en_panne')->count();
        $enMaintenance = (clone $query)->where('statut', 'en_maintenance')->count();
        $rebutes = (clone $query)->where('statut', 'rebuté')->count();

        return [
            'total' => $totalMateriels,
            'disponible' => $disponibles,
            'attribue' => $attribues,
            'en_panne' => $enPanne,
            'en_maintenance' => $enMaintenance,
            'rebute' => $rebutes,
            'taux_disponibilite' => $totalMateriels > 0 ? round(($disponibles / $totalMateriels) * 100, 1) : 0,
            'taux_utilisation' => $totalMateriels > 0 ? round(($attribues / $totalMateriels) * 100, 1) : 0,
        ];
    }

    public function getRepartitionParType(): array
    {
        $data = $this->getFormData();
        $query = Materiel::query()
            ->select('materiel_type_id', DB::raw('count(*) as total'))
            ->with('materielType')
            ->groupBy('materiel_type_id');

        if (!empty($data['statutFiltre'])) {
            $query->where('statut', $data['statutFiltre']);
        }

        return $query->get()
            ->mapWithKeys(fn ($item) => [
                $item->materielType->nom => $item->total,
            ])
            ->toArray();
    }

    public function getRepartitionParService(): array
    {
        $query = Attribution::query()
            ->whereNull('date_restitution')
            ->with(['materiel', 'employee.service'])
            ->get()
            ->groupBy('employee.service.nom');

        $repartition = [];
        foreach ($query as $serviceName => $attributions) {
            $repartition[$serviceName ?? 'Sans service'] = count($attributions);
        }

        return $repartition;
    }

    public function getMaterielsAmortis(): int
    {
        return Materiel::depreciated()->count();
    }

    public function getAttributionsActives(): int
    {
        $data = $this->getFormData();
        $query = Attribution::active();

        if (!empty($data['serviceId'])) {
            $query->whereHas('employee', function ($q) use ($data) {
                $q->where('service_id', $data['serviceId']);
            });
        }

        return $query->count();
    }

    public function getMateriels()
    {
        $data = $this->getFormData();
        $query = Materiel::query()
            ->with(['materielType', 'activeAttribution.employee.service']);

        if (!empty($data['materielTypeId'])) {
            $query->where('materiel_type_id', $data['materielTypeId']);
        }

        if (!empty($data['statutFiltre'])) {
            $query->where('statut', $data['statutFiltre']);
        }

        if (!empty($data['serviceId'])) {
            $query->whereHas('activeAttribution.employee', function ($q) use ($data) {
                $q->where('service_id', $data['serviceId']);
            });
        }

        return $query->orderBy('materiel_type_id')
            ->orderBy('nom')
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Exporter en PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action('exportToPdf'),

            Action::make('exportExcel')
                ->label('Exporter en Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action('exportToExcel'),

            Action::make('actualiser')
                ->label('Actualiser')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->dispatch('$refresh')),
        ];
    }

    public function exportToPdf()
    {
        $data = $this->prepareReportData();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rapport-parc-informatique', $data);

        $filename = 'rapport_parc_' . now()->format('Y-m-d_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function exportToExcel()
    {
        $data = $this->prepareReportData();

        // Pour l'instant, on génère un CSV simple
        $filename = 'rapport_parc_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // En-têtes
            fputcsv($file, ['Type', 'Nom', 'Marque', 'Modèle', 'N° Série', 'Statut', 'État Physique', 'Attribué à', 'Service', 'Date d\'achat']);

            // Données
            foreach ($data['materiels'] as $materiel) {
                fputcsv($file, [
                    $materiel->materielType->nom,
                    $materiel->nom,
                    $materiel->marque,
                    $materiel->modele,
                    $materiel->numero_serie,
                    ucfirst($materiel->statut),
                    ucfirst($materiel->etat_physique ?? 'N/A'),
                    $materiel->activeAttribution?->employee?->full_name ?? 'N/A',
                    $materiel->activeAttribution?->employee?->service?->nom ?? 'N/A',
                    $materiel->purchase_date?->format('d/m/Y') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function prepareReportData(): array
    {
        $data = $this->getFormData();

        return [
            'date_reference' => $data['dateReference'] ?? now()->format('Y-m-d'),
            'date_generation' => now(),
            'filtres' => [
                'type' => !empty($data['materielTypeId']) ? MaterielType::find($data['materielTypeId'])?->nom : 'Tous',
                'service' => !empty($data['serviceId']) ? Service::find($data['serviceId'])?->nom : 'Tous',
                'statut' => !empty($data['statutFiltre']) ? ucfirst($data['statutFiltre']) : 'Tous',
            ],
            'statistiques' => $this->getStatistiquesGlobales(),
            'repartition_type' => $this->getRepartitionParType(),
            'repartition_service' => $this->getRepartitionParService(),
            'materiels_amortis' => $this->getMaterielsAmortis(),
            'attributions_actives' => $this->getAttributionsActives(),
            'materiels' => $this->getMateriels(),
        ];
    }
}
