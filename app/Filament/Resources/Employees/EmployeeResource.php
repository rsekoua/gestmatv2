<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Employees\Pages\ViewEmployee;
use App\Filament\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\Resources\Employees\Tables\EmployeesTable;
use App\Models\Employee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static string|UnitEnum|null $navigationGroup = 'Gestion des Ressources';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Employés';

    protected static ?string $modelLabel = 'Employé';

    protected static ?string $pluralModelLabel = 'Employés';

    protected static ?string $recordTitleAttribute = 'full_name';

    /**
     * Optimize queries with eager loading to prevent N+1 problems.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['service']);
    }

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember('navigation.badge.employees', 300, function () {
            return static::getModel()::count();
        });
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return Cache::remember('navigation.badge.employees.tooltip', 300, function () {
            $count = static::getModel()::count();

            return $count > 1 ? "{$count} employés enregistrés" : "{$count} employé enregistré";
        });
    }

    /**
     * @throws \Exception
     */
    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttributionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'view' => ViewEmployee::route('/{record}'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
