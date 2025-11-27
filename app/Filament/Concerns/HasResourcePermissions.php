<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Str;

trait HasResourcePermissions
{
    /**
     * Get the permission name for the resource.
     */
    protected static function getPermissionName(): string
    {
        // Extraire le nom de la ressource depuis le nom de la classe
        // Ex: MaterielResource -> materiels
        $resourceName = class_basename(static::class);
        $resourceName = Str::replace('Resource', '', $resourceName);

        return Str::snake(Str::plural($resourceName));
    }

    public static function canViewAny(): bool
    {
        $permission = 'view_' . static::getPermissionName();

        return auth()->check() && auth()->user()->can($permission);
    }

    public static function canCreate(): bool
    {
        $permission = 'create_' . static::getPermissionName();

        return auth()->check() && auth()->user()->can($permission);
    }

    public static function canEdit($record): bool
    {
        $permission = 'update_' . static::getPermissionName();

        return auth()->check() && auth()->user()->can($permission);
    }

    public static function canDelete($record): bool
    {
        $permission = 'delete_' . static::getPermissionName();

        return auth()->check() && auth()->user()->can($permission);
    }

    public static function canDeleteAny(): bool
    {
        $permission = 'delete_' . static::getPermissionName();

        return auth()->check() && auth()->user()->can($permission);
    }
}
