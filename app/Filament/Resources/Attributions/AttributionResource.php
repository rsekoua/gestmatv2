<?php

namespace App\Filament\Resources\Attributions;

use App\Filament\Resources\Attributions\Pages\CreateAttribution;
use App\Filament\Resources\Attributions\Pages\EditAttribution;
use App\Filament\Resources\Attributions\Pages\ListAttributions;
use App\Filament\Resources\Attributions\Pages\ViewAttribution;
use App\Filament\Resources\Attributions\Schemas\AttributionForm;
use App\Filament\Resources\Attributions\Tables\AttributionsTable;
use App\Models\Attribution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AttributionResource extends Resource
{
    protected static ?string $model = Attribution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Gestion des Attributions';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Attributions';

    protected static ?string $modelLabel = 'Attribution';

    protected static ?string $pluralModelLabel = 'Attributions';

    protected static ?string $recordTitleAttribute = 'numero_decharge_att';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        $activeCount = static::getModel()::active()->count();
        $totalCount = static::getModel()::count();

        if ($totalCount === 0) {
            return 'gray';
        }

        $percentage = ($activeCount / $totalCount) * 100;

        return match (true) {
            $percentage >= 70 => 'success',
            $percentage >= 40 => 'warning',
            default => 'info',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $active = static::getModel()::active()->count();
        $closed = static::getModel()::closed()->count();

        return "{$active} attributions actives | {$closed} clôturées";
    }

    /**
     * @throws \Exception
     */
    public static function form(Schema $schema): Schema
    {
        return AttributionForm::configure($schema);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return AttributionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttributions::route('/'),
            'create' => CreateAttribution::route('/create'),
            'view' => ViewAttribution::route('/{record}'),
            'edit' => EditAttribution::route('/{record}/edit'),
        ];
    }
}
