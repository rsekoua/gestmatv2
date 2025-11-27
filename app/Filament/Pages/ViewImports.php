<?php

namespace App\Filament\Pages;

use Filament\Actions\Imports\Models\Import;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ViewImports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::ArrowDownTray;

    protected string $view = 'filament.pages.view-imports';

    protected static ?string $navigationLabel = 'Historique des Imports';

    protected static ?string $title = 'Historique des Importations';

    protected static string|null|\UnitEnum $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 20;

    public function table(Table $table): Table
    {
        return $table
            ->query(Import::query()->latest())
            ->columns([
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->default('Système'),

                TextColumn::make('importer')
                    ->label('Type d\'import')
                    ->formatStateUsing(fn ($state) => match (true) {
                        str_contains($state, 'MaterielImporter') => 'Matériels',
                        str_contains($state, 'EmployeeImporter') => 'Employés',
                        str_contains($state, 'ServiceImporter') => 'Services',
                        default => class_basename($state),
                    })
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        str_contains($state, 'MaterielImporter') => 'info',
                        str_contains($state, 'EmployeeImporter') => 'success',
                        str_contains($state, 'ServiceImporter') => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('total_rows')
                    ->label('Total lignes')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('successful_rows')
                    ->label('Réussies')
                    ->numeric()
                    ->color('success')
                    ->alignCenter(),

                TextColumn::make('failed_rows')
                    ->label('Échouées')
                    ->numeric()
                    ->color('danger')
                    ->alignCenter(),

                TextColumn::make('processed_rows')
                    ->label('Progression')
                    ->formatStateUsing(function (Import $record) {
                        $percentage = $record->total_rows > 0
                            ? round(($record->processed_rows / $record->total_rows) * 100)
                            : 0;

                        return "{$record->processed_rows}/{$record->total_rows} ({$percentage}%)";
                    })
                    ->badge()
                    ->color(fn (Import $record) => match (true) {
                        $record->processed_rows === $record->total_rows => 'success',
                        $record->processed_rows > 0 => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Terminé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('En cours...'),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('5s');
    }

    public static function canAccess(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole(['super_admin', 'admin', 'gestionnaire'])
        );
    }
}
