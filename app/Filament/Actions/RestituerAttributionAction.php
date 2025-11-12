<?php

namespace App\Filament\Actions;

use App\Models\Attribution;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class RestituerAttributionAction
{
    public static function make(): Action
    {
        return Action::make('restituer')
            ->label('Restituer')
            ->icon(Heroicon::ArrowUturnLeft)
            ->color('warning')
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
                            ->closeOnDateSelection(),

                        Textarea::make('observations_res')
                            ->label('Observations')
                            ->rows(3)
                            ->placeholder('Observations générales sur la restitution...')
                            ->columnSpanFull(),
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
                            ->default('bon'),

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
                            ->default('parfait'),

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
