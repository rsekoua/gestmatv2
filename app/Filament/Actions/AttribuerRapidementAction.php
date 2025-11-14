<?php

namespace App\Filament\Actions;

use App\Models\Accessory;
use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class AttribuerRapidementAction
{
    public static function make(): Action
    {
        return Action::make('attribuer_rapidement')
            ->label('Attribuer rapidement')
            ->icon(Heroicon::Bolt)
            ->color('success')
            ->visible(fn (Materiel $record): bool => $record->statut === 'disponible')
            ->requiresConfirmation()
            ->modalHeading('Attribuer le matériel')
            ->modalDescription(fn (Materiel $record): string =>
                "Matériel : {$record->numero_serie} ({$record->marque} {$record->modele})"
            )
            ->modalSubmitActionLabel('Créer l\'attribution')
            ->modalWidth('2xl')
            ->Schema([
                Section::make('Informations d\'attribution')
                    ->schema([
                        Select::make('employee_id')
                            ->label('Employé')
                            ->required()
                            ->searchable(['nom', 'prenom', 'matricule'])
                            ->options(function () {
                                return Employee::with('service')
                                    ->get()
                                    ->mapWithKeys(function (Employee $employee) {
                                        $serviceName = $employee->service?->code ?? 'Sans service';
                                        return [$employee->id => "{$employee->full_name} - ({$serviceName})"];
                                    });
                            })
                            ->placeholder('Sélectionnez un employé'),

                        DatePicker::make('date_attribution')
                            ->label('Date d\'attribution')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->closeOnDateSelection(),

                        Textarea::make('observations_att')
                            ->label('Observations')
                            ->rows(3)
                            ->placeholder('Observations sur cette attribution...')
                            ->columnSpanFull(),
                    ]),

                Section::make('Accessoires')
                    ->schema([
                        CheckboxList::make('accessories')
                            ->label('Accessoires fournis')
                            ->options(Accessory::pluck('nom', 'id'))
                            ->descriptions(
                                Accessory::all()
                                    ->pluck('description', 'id')
                                    ->filter()
                                    ->toArray()
                            )
                            ->columns(2)
                            ->searchable()
                            ->bulkToggleable()
                            ->gridDirection('row'),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ])
            ->action(function (Materiel $record, array $data): void {
                // Extract accessories from data
                $accessories = $data['accessories'] ?? [];
                unset($data['accessories']);

                // Create the attribution
                $attribution = Attribution::create([
                    'materiel_id' => $record->id,
                    'employee_id' => $data['employee_id'],
                    'date_attribution' => $data['date_attribution'],
                    'observations_att' => $data['observations_att'] ?? null,
                ]);

                // Attach accessories if any
                if (! empty($accessories)) {
                    $attribution->accessories()->attach($accessories);
                }

                Notification::make()
                    ->title('Attribution créée')
                    ->success()
                    ->body("Le matériel {$record->numero_serie} a été attribué à {$attribution->employee->full_name}.")
                    ->send();
            });
    }
}
