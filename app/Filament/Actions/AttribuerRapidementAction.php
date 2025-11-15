<?php

namespace App\Filament\Actions;

use App\Filament\Concerns\ManagesAccessories;
use App\Models\Accessory;
use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class AttribuerRapidementAction
{
    use ManagesAccessories;

    public static function make(): Action
    {
        return Action::make('attribuer_rapidement')
            ->label('Attribuer rapidement')
            ->icon(Heroicon::Bolt)
            ->color('success')
            ->visible(fn (Materiel $record): bool => $record->statut === 'disponible')
            ->requiresConfirmation()
            ->modalHeading('Attribuer le matériel')
            ->modalDescription(fn (Materiel $record): string => "{$record->materielType->nom} - {$record->marque} {$record->modele}"
            )
            ->modalSubmitActionLabel('Créer l\'attribution')
            ->modalWidth('2xl')
            ->Schema([
                Section::make('Informations d\'attribution')
                    ->description(fn (Materiel $record) => $record->materielType->isComputer()
                        ? 'Les ordinateurs sont attribués aux employés'
                        : 'Les autres équipements sont attribués aux services'
                    )
                    ->icon(Heroicon::DocumentText)
                    ->schema([
                        // Champ Employé (visible uniquement pour les ordinateurs)
                        Select::make('employee_id')
                            ->label('Employé')
                            ->required(fn (Materiel $record): bool => $record->materielType->isComputer())
                            ->visible(fn (Materiel $record): bool => $record->materielType->isComputer())
                            ->searchable(['nom', 'prenom', 'matricule'])
                            ->preload()
                            ->options(function () {
                                return Employee::with('service')
                                    ->get()
                                    ->mapWithKeys(function (Employee $employee) {
                                        $serviceName = $employee->service?->code ?? 'Sans service';

                                        return [$employee->id => "{$employee->full_name} - ({$serviceName})"];
                                    });
                            })
                            ->placeholder('Sélectionnez un employé')
                            ->helperText('Employé qui recevra le matériel'),

                        // Champ Service (visible uniquement pour les non-ordinateurs)
                        Select::make('service_id')
                            ->label('Service')
                            ->required(fn (Materiel $record): bool => ! $record->materielType->isComputer())
                            ->visible(fn (Materiel $record): bool => ! $record->materielType->isComputer())
                            ->searchable(['nom', 'code'])
                            ->preload()
                            ->options(function () {
                                return Service::query()
                                    ->orderBy('nom')
                                    ->get()
                                    ->mapWithKeys(function (Service $service) {
                                        return [$service->id => $service->full_name];
                                    });
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $service = Service::find($state);
                                    $set('responsable_service', $service?->responsable);
                                } else {
                                    $set('responsable_service', null);
                                }
                            })
                            ->placeholder('Sélectionnez un service')
                            ->helperText('Service qui recevra le matériel'),

                        // Champ Responsable du service (visible uniquement pour les non-ordinateurs)
                        TextInput::make('responsable_service')
                            ->label('Responsable du Service')
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn (Materiel $record): bool => ! $record->materielType->isComputer())
                            ->helperText('Nom du chef de service (rempli automatiquement)'),

                        DatePicker::make('date_attribution')
                            ->label('Date d\'attribution')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->closeOnDateSelection()
                            ->helperText('Date de prise en charge du matériel'),

                        Textarea::make('observations_att')
                            ->label('Observations')
                            ->rows(3)
                            ->placeholder('Notes ou observations concernant l\'attribution')
                            ->columnSpanFull(),
                    ]),

                Section::make('Accessoires')
                    ->description('Sélectionnez les accessoires associés')
                    ->icon(Heroicon::CpuChip)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        ToggleButtons::make('accessories')
                            ->label('Accessoires associés')
                            ->multiple()
                            ->options(Accessory::pluck('nom', 'id'))
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ]),
                    ]),
            ])
            ->action(function (Materiel $record, array $data): void {
                // Extract accessories from data
                $accessories = $data['accessories'] ?? [];
                unset($data['accessories']);

                // Create the attribution
                $attribution = Attribution::create([
                    'materiel_id' => $record->id,
                    'employee_id' => $data['employee_id'] ?? null,
                    'service_id' => $data['service_id'] ?? null,
                    'responsable_service' => $data['responsable_service'] ?? null,
                    'date_attribution' => $data['date_attribution'],
                    'observations_att' => $data['observations_att'] ?? null,
                ]);

                // Utiliser le trait pour attacher les accessoires avec les données pivot
                (new self)->attachAccessories($attribution, $accessories);

                // Préparer le message de notification
                $recipient = $attribution->isForEmployee()
                    ? "à {$attribution->employee->full_name}"
                    : "au service {$attribution->service->nom}";

                Notification::make()
                    ->title('Attribution créée')
                    ->success()
                    ->body("Le matériel {$record->numero_serie} a été attribué {$recipient}.")
                    ->send();
            });
    }
}
