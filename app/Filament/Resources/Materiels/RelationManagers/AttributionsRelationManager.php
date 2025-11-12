<?php

namespace App\Filament\Resources\Materiels\RelationManagers;

use App\Models\Attribution;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttributionsRelationManager extends RelationManager
{
    protected static string $relationship = 'attributions';

    protected static ?string $title = 'Historique des Attributions';

    protected static ?string $recordTitleAttribute = 'numero_decharge_att';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_decharge_att')
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

                TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->icon(Heroicon::User)
                    ->iconColor('success')
                    ->searchable(['nom', 'prenom'])
                    ->sortable()
                    ->description(fn ($record): string => $record->employee?->service?->nom ?? 'Aucun service')
                    ->wrap()
                    ->url(fn ($record): string => $record->employee
                        ? route('filament.admin.resources.employees.view', ['record' => $record->employee])
                        : '#'
                    ),

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
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state < 30 => 'success',
                        $state < 180 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('etat_fonctionnel_res')
                    ->label('État Retour')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'parfait' => 'success',
                        'defauts_mineurs' => 'success',
                        'dysfonctionnements' => 'warning',
                        'hors_service' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'parfait' => 'Parfait',
                        'defauts_mineurs' => 'Défauts mineurs',
                        'dysfonctionnements' => 'Dysfonctionnements',
                        'hors_service' => 'Hors service',
                        default => '—',
                    })
                    ->placeholder('—')
                    ->toggleable(),

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
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('Statut')
                    ->placeholder('Toutes')
                    ->trueLabel('Actives')
                    ->falseLabel('Clôturées')
                    ->queries(
                        true: fn (Builder $query) => $query->active(),
                        false: fn (Builder $query) => $query->closed(),
                    ),
            ])
            ->headerActions([
                // On peut ajouter une action pour créer une attribution depuis le matériel
            ])
            ->recordActions([
                Action::make('restituer')
                    ->icon(Heroicon::ArrowUturnLeft)
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Restituer')
                    ->visible(fn (Attribution $record): bool => $record->isActive())
                    ->requiresConfirmation()
                    ->modalHeading('Restituer le matériel')
                    ->modalSubmitActionLabel('Confirmer')
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
                                    ->default('remis_en_stock'),

                                Textarea::make('dommages_res')
                                    ->label('Dommages constatés')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->action(function (Attribution $record, array $data): void {
                        $record->update($data);
                        Notification::make()
                            ->title('Restitution enregistrée')
                            ->success()
                            ->send();
                    }),
                ViewAction::make()
                    ->iconButton()
                    ->url(fn ($record): string => route('filament.admin.resources.attributions.view', ['record' => $record])),
            ])
            ->bulkActions([
                // Pas d'actions en masse sur les attributions depuis cette vue
            ])
            ->emptyStateHeading('Aucune attribution')
            ->emptyStateDescription('Ce matériel n\'a jamais été attribué.')
            ->emptyStateIcon(Heroicon::ArrowsRightLeft)
            ->defaultSort('date_attribution', 'desc')
            ->paginated([10, 25, 50]);
    }
}
