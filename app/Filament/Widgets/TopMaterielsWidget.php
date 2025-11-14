<?php

namespace App\Filament\Widgets;

use App\Models\Materiel;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopMaterielsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Matériels - Attributions';

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = [
        'default' => 12,
        'sm' => 12,
        'md' => 12,
        'lg' => 6,
        'xl' => 6,
        '2xl' => 6,
    ];

    protected static ?string $pollingInterval = '60s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Materiel::query()
                    ->has('attributions')
                    ->withCount('attributions')
                    ->with(['materielType', 'activeAttribution'])
                    ->orderBy('attributions_count', 'desc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('rank')
                    ->label('#')
                    ->state(
                        fn ($rowLoop) => $rowLoop->iteration
                    )
                    ->badge()
                    ->color(fn ($rowLoop) => match ($rowLoop->iteration) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'primary',
                        default => 'info',
                    }),

                TextColumn::make('nom')
                    ->label('Matériel')
                    ->searchable()
                    ->description(fn (Materiel $record) => $record->numero_serie)
                    ->icon('heroicon-o-computer-desktop')
                    ->weight('bold'),

                TextColumn::make('materielType.nom')
                    ->label('Type')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-tag'),

                TextColumn::make('attributions_count')
                    ->label('Attributions')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-cube'),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'disponible' => 'success',
                        'attribué' => 'info',
                        'en_panne' => 'danger',
                        'en_maintenance' => 'warning',
                        'rebuté' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->icon(fn ($state) => match ($state) {
                        'disponible' => 'heroicon-o-check-circle',
                        'attribué' => 'heroicon-o-arrows-right-left',
                        'en_panne' => 'heroicon-o-exclamation-triangle',
                        'en_maintenance' => 'heroicon-o-wrench-screwdriver',
                        'rebuté' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
            ])
            ->paginated(false);
    }
}
