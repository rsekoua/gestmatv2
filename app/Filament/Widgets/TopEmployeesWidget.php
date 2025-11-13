<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopEmployeesWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Employés - Attributions';

    protected static ?int $sort = 6;

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
                Employee::query()
                    ->has('attributions')
                    ->withCount(['attributions', 'activeAttributions'])
                    ->with('service')
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

                TextColumn::make('full_name')
                    ->label('Employé')
                    ->searchable(['nom', 'prenom'])
                    ->description(fn (Employee $record) => $record->service->nom ?? 'Sans service')
                    ->icon(Heroicon::User)
                    ->weight('bold'),

                TextColumn::make('attributions_count')
                    ->label('Total')
                    ->suffix(' attributions')
                    ->badge()
                    ->color('primary')
                    ->icon(Heroicon::Cube),

                TextColumn::make('active_attributions_count')
                    ->label('Actives')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->icon(Heroicon::CheckCircle),
            ])
            ->paginated(false);
    }
}
