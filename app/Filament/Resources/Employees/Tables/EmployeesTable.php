<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Models\Service;
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

class EmployeesTable
{
    /**
     * @throws \Exception
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nom Complet')
                    ->icon(Heroicon::UserCircle)
                    ->iconColor('primary')
                    ->weight(FontWeight::Bold)
                    ->searchable(['nom', 'prenom'])
                    ->sortable(['nom', 'prenom'])
                    ->getStateUsing(fn ($record) => $record->full_name)
                    ->description(fn ($record): string => $record->email)
                    ->wrap(),

                TextColumn::make('service.nom')
                    ->label('Service')
                    ->icon(Heroicon::BuildingOffice2)
                    ->iconColor('success')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non assigné')
                    ->toggleable(),

                TextColumn::make('emploi')
                    ->label('Emploi')
                    ->icon(Heroicon::Briefcase)
                    ->iconColor('info')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('fonction')
                    ->label('Fonction')
                    ->icon(Heroicon::Identification)
                    ->iconColor('warning')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->icon(Heroicon::Phone)
                    ->iconColor('gray')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Téléphone copié!')
                    ->copyMessageDuration(1500),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon(Heroicon::Envelope)
                    ->iconColor('gray')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->copyMessage('Email copié!')
                    ->copyMessageDuration(1500),

                TextColumn::make('attributions_count')
                    ->counts('attributions')
                    ->label('Attributions')
                    ->icon(Heroicon::Cube)
                    ->iconColor('gray')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Nombre total d\'attributions')
                    ->toggleable(),

                TextColumn::make('active_attributions_count')
                    ->counts('activeAttributions')
                    ->label('Actives')
                    ->icon(Heroicon::CheckCircle)
                    ->iconColor('success')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Attributions actives (non restituées)')
                    ->badge()
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'gray')
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
                SelectFilter::make('service_id')
                    ->label('Filtrer par service')
                    ->relationship('service', 'nom')
                    ->searchable()
                    ->preload()
                    ->placeholder('Tous les services'),

                SelectFilter::make('emploi')
                    ->label('Filtrer par emploi')
                    ->options(fn (): array => \App\Models\Employee::query()
                        ->whereNotNull('emploi')
                        ->distinct()
                        ->pluck('emploi', 'emploi')
                        ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Tous les emplois'),

                SelectFilter::make('fonction')
                    ->label('Filtrer par fonction')
                    ->options(fn (): array => \App\Models\Employee::query()
                        ->whereNotNull('fonction')
                        ->distinct()
                        ->pluck('fonction', 'fonction')
                        ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Toutes les fonctions'),

                SelectFilter::make('has_attributions')
                    ->label('Avec/sans attributions')
                    ->options([
                        'with' => 'Avec attributions',
                        'without' => 'Sans attributions',
                        'active' => 'Avec attributions actives',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'with' => $query->has('attributions'),
                        'without' => $query->doesntHave('attributions'),
                        'active' => $query->has('activeAttributions'),
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
            ->emptyStateHeading('Aucun employé trouvé')
            ->emptyStateDescription('Commencez par créer votre premier employé en cliquant sur le bouton ci-dessous.')
            ->emptyStateIcon(Heroicon::Users)
            ->defaultSort('nom', 'asc')
            ->striped();
//            ->recordUrl(null);
    }
}
