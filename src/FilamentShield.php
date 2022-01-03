<?php

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FilamentShield
{
    public static function generateFor(string $model): void
    {
        $permissions = collect();
        collect(config('filament-shield.default_permission_prefixes'))
            ->each(function ($prefix) use ($model, $permissions) {
                $permissions->push(Permission::firstOrCreate(
                    ['name' => $prefix.'_'.Str::lower($model)],
                    ['guard_name' => config('filament.auth.guard')]
                ));
            });

        if (static::isSuperAdminEnabled()) {
            $superAdmin = Role::firstOrCreate(
                ['name' => config('filament-shield.super_admin.role_name')],
                ['guard_name' => config('filament.auth.guard')]
            );
            $superAdmin->givePermissionTo($permissions);
        }
    }

    public static function isSuperAdminEnabled(): bool
    {
        return config('filament-shield.super_admin.enabled');
    }

    public static function isFilamentUserEnabled(): bool
    {
        return config('filament-shield.filament_user.enabled');
    }
}
