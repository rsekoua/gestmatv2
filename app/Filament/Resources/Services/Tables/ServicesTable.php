<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\Action;
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

class ServicesTable
{
    /**
     * @throws \Exception
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom du Service')
                    ->icon(Heroicon::BuildingOffice2)
                    ->iconColor('primary')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->responsable ? "Responsable: {$record->responsable}" : 'Aucun responsable assigné')
                    ->wrap(),

                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('info')
                    ->icon(Heroicon::Tag)
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('responsable')
                    ->label('Responsable')
                    ->icon(Heroicon::User)
                    ->iconColor('success')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non défini')
                    ->toggleable(),

                TextColumn::make('employees_count')
                    ->counts('employees')
                    ->label('Employés')
                    ->icon(Heroicon::Users)
                    ->iconColor('gray')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Nombre d\'employés dans ce service'),

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
                SelectFilter::make('responsable')
                    ->label('Filtrer par responsable')
                    ->options(fn (): array => \App\Models\Service::query()
                        ->whereNotNull('responsable')
                        ->pluck('responsable', 'responsable')
                        ->toArray()
                    )
                    ->placeholder('Tous les responsables'),

                SelectFilter::make('has_employees')
                    ->label('Avec/sans employés')
                    ->options([
                        'with' => 'Avec employés',
                        'without' => 'Sans employés',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'with' => $query->has('employees'),
                        'without' => $query->doesntHave('employees'),
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
            ->emptyStateHeading('Aucun service trouvé')
            ->emptyStateDescription('Commencez par créer votre premier service en cliquant sur le bouton ci-dessous.')
            ->emptyStateIcon(Heroicon::BuildingOffice2)
            ->defaultSort('nom', 'asc')
            ->striped()
            ->recordUrl(null);
    }
}
