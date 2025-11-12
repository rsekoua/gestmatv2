<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttributionsRelationManager extends RelationManager
{
    protected static string $relationship = 'attributions';

    protected static ?string $title = 'Attributions';

    protected static ?string $recordTitleAttribute = 'numero_decharge_att';

    /**
     * @throws \Exception
     */
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

                TextColumn::make('materiel.nom')
                    ->label('Matériel')
                    ->icon(Heroicon::ComputerDesktop)
                    ->iconColor('info')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->materiel?->numero_serie ?? '—')
                    ->wrap()
                    ->url(fn ($record): string => $record->materiel
                        ? route('filament.admin.resources.materiels.materials.view', ['record' => $record->materiel])
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
                // On peut ajouter une action pour créer une attribution depuis l'employé
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => route('filament.admin.resources.attributions.view', ['record' => $record])),
            ])
            ->toolbarActions([
                // Pas d'actions en masse sur les attributions depuis cette vue
            ])
            ->emptyStateHeading('Aucune attribution')
            ->emptyStateDescription('Cet employé n\'a pas encore d\'attribution.')
            ->emptyStateIcon(Heroicon::ArrowsRightLeft)
            ->defaultSort('date_attribution', 'desc')
            ->paginated([10, 25, 50]);
    }
}
