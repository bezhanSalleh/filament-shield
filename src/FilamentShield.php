<?php

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FilamentShield
{
    public static function generateFor(string $model): void
    {
        $permissions = collect();
        collect(config('filament-shield.default_permission_prefixes'))
            ->each(function ($prefix) use ($model, $permissions) {
                $permissions->push(Permission::firstOrCreate([
                    'name' => $prefix.'_'.Str::lower($model),
                ]));
            });

        if (static::checkIfSuperAdminIsEnabled()) {
            $superAdmin = Role::firstOrCreate([
                'name' => config('filament-shield.default_roles.super_admin.role_name'),
            ]);
            $superAdmin->givePermissionTo($permissions);
        }
    }

    protected static function checkIfSuperAdminIsEnabled(): bool
    {
        return config('filament-shield.default_roles.super_admin.enabled');
    }
}
