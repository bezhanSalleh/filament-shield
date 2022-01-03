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

        $superAdmin = Role::firstOrCreate(
            ['name' => config('filament-shield.default_roles.super_admin_role_name')],
            ['guard_name' => config('filament.auth.guard')]
        );

        $superAdmin->givePermissionTo($permissions);

    }
}
