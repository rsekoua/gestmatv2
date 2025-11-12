<?php

namespace App\Filament\Resources\Attributions\Tables;

use App\Models\Attribution;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttributionsTable
{
    /**
     * @throws \Exception
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_decharge_att')
                    ->label('N° Décharge')
                    ->icon(Heroicon::QrCode)
                    ->iconColor('primary')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Numéro copié!')
                    ->copyMessageDuration(1500),

                TextColumn::make('materiel.nom')
                    ->label('Matériel')
                    ->icon(Heroicon::ComputerDesktop)
                    ->iconColor('info')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->materiel?->numero_serie ?? '—')
                    ->wrap(),

                TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->icon(Heroicon::User)
                    ->iconColor('success')
                    ->searchable(['nom', 'prenom'])
                    ->sortable()
                    ->description(fn ($record): string => $record->employee?->service?->nom ?? 'Aucun service')
                    ->wrap(),

                TextColumn::make('date_attribution')
                    ->label('Date Attribution')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon(Heroicon::Calendar)
                    ->tooltip(fn ($record): string => $record->date_attribution->diffForHumans()),

                TextColumn::make('date_restitution')
                    ->label('Date Restitution')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon(Heroicon::Calendar)
                    ->placeholder('En cours')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state): string => $state ? $state->format('d/m/Y') : 'En cours')
                    ->tooltip(fn ($record): ?string => $record->date_restitution?->diffForHumans()),

                TextColumn::make('duration_in_days')
                    ->label('Durée')
                    ->suffix(' jours')
                    ->icon(Heroicon::Clock)
                    ->iconColor('gray')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Durée de l\'attribution'),

                TextColumn::make('decision_res')
                    ->label('Décision')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'remis_en_stock' => 'success',
                        'a_reparer' => 'warning',
                        'rebut' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'remis_en_stock' => 'Remis en stock',
                        'a_reparer' => 'À réparer',
                        'rebut' => 'Rebut',
                        default => '—',
                    })
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon(Heroicon::Clock)
                    ->tooltip(fn ($record): string => $record->created_at->diffForHumans()),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('Statut')
                    ->placeholder('Tous')
                    ->trueLabel('Actives')
                    ->falseLabel('Clôturées')
                    ->queries(
                        true: fn (Builder $query) => $query->active(),
                        false: fn (Builder $query) => $query->closed(),
                    ),

                SelectFilter::make('materiel_id')
                    ->label('Matériel')
                    ->relationship('materiel', 'numero_serie')
                    ->searchable(['numero_serie', 'marque', 'modele'])
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} ({$record->numero_serie})"),

                SelectFilter::make('employee_id')
                    ->label('Employé')
                    ->relationship('employee', 'nom')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name),

                SelectFilter::make('decision_res')
                    ->label('Décision de restitution')
                    ->options([
                        'remis_en_stock' => 'Remis en Stock',
                        'a_reparer' => 'À Réparer',
                        'rebut' => 'Rebut',
                    ]),
            ])
            ->recordActions([
                Action::make('restituer')
                    ->label('Restituer')
                    ->icon(Heroicon::ArrowUturnLeft)
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Restituer le matériel')
                    ->visible(fn (Attribution $record): bool => $record->isActive())
                    ->requiresConfirmation()
                    ->modalHeading('Restituer le matériel')
                    ->modalSubmitActionLabel('Confirmer la restitution')
                    ->modalWidth('2xl')
                    ->form([
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
                    }),
                ViewAction::make()
                    ->iconButton(),
                EditAction::make()
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('Aucune attribution trouvée')
            ->emptyStateDescription('Commencez par créer votre première attribution en cliquant sur le bouton ci-dessous.')
            ->emptyStateIcon(Heroicon::ArrowsRightLeft)
            ->defaultSort('date_attribution', 'desc')
            ->striped();
    }
}
