<?php

namespace App\Filament\Resources\MaintenanceOperations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaintenanceOperationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('materiel.nom')
                    ->label('Matériel')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('definition.label')
                    ->label('Type de Maintenance')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'À faire',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                    })
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->label('Date Prévue')
                    ->date()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label('Date Réalisation')
                    ->date()
                    ->sortable(),
                TextColumn::make('performer.name')
                    ->label('Réalisé par')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
