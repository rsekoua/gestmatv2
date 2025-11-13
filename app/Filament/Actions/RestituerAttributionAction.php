<?php

namespace App\Filament\Actions;

use App\Models\Attribution;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class RestituerAttributionAction
{
    /**
     * @throws \Exception
     */
    public static function make(): Action
    {
        return Action::make('-')
            //->label('Restituer')
            ->icon(Heroicon::ArrowUturnLeft)
            ->color('danger')
            ->visible(fn (Attribution $record): bool => $record->isActive())
            ->requiresConfirmation()
            ->modalHeading('Restituer le matériel')
            ->modalDescription(fn (Attribution $record): string => "Matériel : {$record->materiel->numero_serie} | Employé : {$record->employee->full_name}"
            )
            ->modalSubmitActionLabel('Confirmer la restitution')
            ->modalWidth('2xl')
            ->Schema([
                Section::make('Informations de restitution')
                    ->schema([
                        DatePicker::make('date_restitution')
                            ->label('Date de restitution')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->closeOnDateSelection()
                            ->afterOrEqual(fn (Attribution $record) => $record->date_attribution)
                            ->validationMessages([
                                'after_or_equal' => 'La date de restitution doit être postérieure à celle de l\'attribution.',
                                'required' => 'La date de restitution est obligatoire.',
                            ]),

                        Textarea::make('observations_res')
                            ->label('Observations')
                            ->required()
                            ->rows(3)
                            ->placeholder('Observations générales sur la restitution...')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Les observations de restitution sont obligatoires.',
                            ]),
                    ]),

                Section::make('État du matériel')
                    ->schema([
                        Radio::make('etat_general_res')
                            ->label('État général')
                            ->required()
                            ->options([
                                'excellent' => 'Excellent',
                                'bon' => 'Bon',
                                'moyen' => 'Moyen',
                                'mauvais' => 'Mauvais',
                            ])
                            ->inline()
                            ->inlineLabel(false)
                            ->default('bon')
                            ->validationMessages([
                                'required' => 'L\'état général est obligatoire lors de la restitution.',
                            ]),

                        Radio::make('etat_fonctionnel_res')
                            ->label('État fonctionnel')
                            ->required()
                            ->options([
                                'parfait' => 'Parfait',
                                'defauts_mineurs' => 'Défauts mineurs',
                                'dysfonctionnements' => 'Dysfonctionnements',
                                'hors_service' => 'Hors service',
                            ])
                            ->inline()
                            ->inlineLabel(false)
                            ->default('parfait')
                            ->validationMessages([
                                'required' => 'L\'état fonctionnel est obligatoire lors de la restitution.',
                            ]),

                        Radio::make('decision_res')
                            ->label('Décision')
                            ->required()
                            ->options([
                                'remis_en_stock' => 'Remis en stock',
                                'a_reparer' => 'À réparer',
                                'rebut' => 'Rebut',
                            ])
                            ->inline()
                            ->inlineLabel(false)
                            ->default('remis_en_stock')
                            ->helperText('Que devient le matériel après restitution ?'),

                        Textarea::make('dommages_res')
                            ->label('Dommages constatés')
                            ->rows(3)
                            ->placeholder('Décrivez les dommages éventuels...')
                            ->columnSpanFull(),
                    ]),
            ])
            ->action(function (Attribution $record, array $data): void {
                $record->update($data);

                Notification::make()
                    ->title('Restitution enregistrée')
                    ->success()
                    ->body("Le matériel {$record->materiel->numero_serie} a été restitué avec succès.")
                    ->send();
            });
    }
}
