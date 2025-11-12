<?php

namespace App\Filament\Resources\Attributions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
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
                        'maintien_en_service' => 'success',
                        'reparation' => 'warning',
                        'reforme' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'maintien_en_service' => 'Maintien',
                        'reparation' => 'Réparation',
                        'reforme' => 'Réforme',
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
                        'maintien_en_service' => 'Maintien en Service',
                        'reparation' => 'Réparation',
                        'reforme' => 'Réforme',
                    ]),
            ])
            ->recordActions([
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
