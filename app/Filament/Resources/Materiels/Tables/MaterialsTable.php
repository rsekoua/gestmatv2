<?php

namespace App\Filament\Resources\Materiels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaterialsTable
{
    /**
     * @throws \Exception
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label('Désignation')
                    ->icon(Heroicon::ComputerDesktop)
                    ->iconColor('primary')
                    ->weight(FontWeight::Bold)
                    ->searchable(['marque', 'modele'])
                    ->sortable(['marque', 'modele'])
                    ->getStateUsing(fn ($record) => $record->nom)
                    ->description(fn ($record): string => $record->numero_serie ? "S/N: {$record->numero_serie}" : 'Aucun numéro de série')
                    ->wrap(),

                TextColumn::make('materielType.nom')
                    ->label('Type')
                    ->icon(Heroicon::Tag)
                    ->iconColor('info')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('specifications_summary')
                    ->label('Spécifications')
                    ->icon(Heroicon::CpuChip)
                    ->iconColor('gray')
                    ->getStateUsing(fn ($record) => $record->specifications_summary)
                    ->placeholder('Aucune spécification')
                    ->wrap()
                    ->toggleable()
                    ->tooltip('CPU | RAM | Stockage | Écran'),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'disponible' => 'success',
                        'attribué' => 'warning',
                        'en_panne' => 'danger',
                        'en_maintenance' => 'info',
                        'rebuté' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'disponible' => Heroicon::CheckCircle,
                        'attribué' => Heroicon::UserCircle,
                        'en_panne' => Heroicon::XCircle,
                        'en_maintenance' => Heroicon::Wrench,
                        'rebuté' => Heroicon::ArchiveBox,
                        default => Heroicon::QuestionMarkCircle,
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en_panne' => 'En panne',
                        'en_maintenance' => 'En maintenance',
                        'attribué' => 'Attribué',
                        'disponible' => 'Disponible',
                        'rebuté' => 'Rebuté',
                        default => ucfirst($state),
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('etat_physique')
                    ->label('État Physique')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'excellent' => 'success',
                        'bon' => 'success',
                        'moyen' => 'warning',
                        'mauvais' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('purchase_date')
                    ->label('Date d\'Achat')
                    ->date('d/m/Y')
                    ->icon(Heroicon::Calendar)
                    ->sortable()
                    ->toggleable()
                    ->tooltip(fn ($record): ?string => $record->purchase_date ? $record->purchase_date->diffForHumans() : null),

                TextColumn::make('acquision')
                    ->label('Mode d\'Acquisition')
                    ->icon(Heroicon::ShoppingBag)
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('amortissement_status')
                    ->label('Amortissement')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Amorti' => 'danger',
                        'Actif' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'Amorti' => Heroicon::ExclamationTriangle,
                        'Actif' => Heroicon::CheckCircle,
                        default => Heroicon::QuestionMarkCircle,
                    })
                    ->getStateUsing(fn ($record): string => $record->amortissement_status)
                    ->sortable()
                    ->toggleable()
                    ->tooltip('Amorti après 3 ans pour les ordinateurs'),

                TextColumn::make('attributions_count')
                    ->counts('attributions')
                    ->label('Attributions')
                    ->icon(Heroicon::UserGroup)
                    ->iconColor('gray')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Nombre total d\'attributions')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon(Heroicon::Clock)
                    ->tooltip(fn ($record): string => $record->created_at->diffForHumans()),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon(Heroicon::PencilSquare)
                    ->tooltip(fn ($record): string => $record->updated_at->diffForHumans()),
            ])
            ->filters([
                SelectFilter::make('materiel_type_id')
                    ->label('Filtrer par type')
                    ->relationship('materielType', 'nom')
                    ->searchable()
                    ->preload()
                    ->placeholder('Tous les types'),

                SelectFilter::make('statut')
                    ->label('Filtrer par statut')
                    ->options([
                        'disponible' => 'Disponible',
                        'attribué' => 'Attribué',
                        'en_panne' => 'En panne',
                        'en_maintenance' => 'En maintenance',
                        'rebuté' => 'Rebuté',
                    ])
                    ->placeholder('Tous les statuts'),

                SelectFilter::make('etat_physique')
                    ->label('Filtrer par état physique')
                    ->options([
                        'excellent' => 'Excellent',
                        'bon' => 'Bon',
                        'moyen' => 'Moyen',
                        'mauvais' => 'Mauvais',
                    ])
                    ->placeholder('Tous les états'),

                SelectFilter::make('marque')
                    ->label('Filtrer par marque')
                    ->options(fn (): array => \App\Models\Materiel::query()
                        ->whereNotNull('marque')
                        ->distinct()
                        ->pluck('marque', 'marque')
                        ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Toutes les marques'),

                SelectFilter::make('amortissement')
                    ->label('Filtrer par amortissement')
                    ->options([
                        'amorti' => 'Amorti (> 3 ans)',
                        'actif' => 'Actif (< 3 ans)',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'amorti' => $query->depreciated(),
                        'actif' => $query->whereHas('materielType', function ($q) {
                            $q->whereIn('nom', ['Ordinateur Portable', 'Ordinateur Bureau']);
                        })
                        ->whereDate('purchase_date', '>', now()->subYears(3)),
                        default => $query,
                    }),

                SelectFilter::make('has_attributions')
                    ->label('Avec/sans attributions')
                    ->options([
                        'with' => 'Avec attributions',
                        'without' => 'Sans attributions',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'with' => $query->has('attributions'),
                        'without' => $query->doesntHave('attributions'),
                        default => $query,
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),
                EditAction::make()
                    ->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('Aucun matériel trouvé')
            ->emptyStateDescription('Commencez par créer votre premier matériel en cliquant sur le bouton ci-dessous.')
            ->emptyStateIcon(Heroicon::ComputerDesktop)
            ->defaultSort('created_at', 'desc')
            ->striped();
           // ->recordUrl(null);
    }
}
