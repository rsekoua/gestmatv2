<?php

namespace App\Filament\Widgets;

use App\Models\Attribution;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAttributionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Attributions Récentes';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = [
        'default' => 12,
        'sm' => 12,
        'md' => 12,
        'lg' => 12,
        'xl' => 12,
        '2xl' => 12,
    ];

    protected static ?string $pollingInterval = '60s';

    /**
     * @throws \Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attribution::query()
                    ->with(['materiel.materielType', 'employee.service'])
                    ->latest('date_attribution')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('numero_decharge_att')
                    ->label('N° Décharge')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-qr-code'),

                TextColumn::make('materiel.nom')
                    ->label('Matériel')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Attribution $record) => $record->materiel->numero_serie)
                    ->icon('heroicon-o-computer-desktop'),

                TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Attribution $record) => $record->employee->service->nom ?? 'Sans service')
                    ->icon('heroicon-o-user'),

                TextColumn::make('date_attribution')
                    ->label('Date Attribution')
                    ->date('d/m/Y')
                    ->sortable()
                    ->since()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('date_restitution')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state === null ? 'Active' : 'Clôturée')
                    ->icon(fn ($state) => $state === null ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

                TextColumn::make('duration_in_days')
                    ->label('Durée')
                    ->suffix(' jours')
                    ->badge()
                    ->color(fn ($state, Attribution $record) => match (true) {
                        $record->date_restitution !== null => 'gray',
                        $state < 30 => 'success',
                        $state < 180 => 'warning',
                        default => 'danger',
                    })
                    ->icon('heroicon-o-clock'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Attribution $record) => route('filament.admin.resources.attributions.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
