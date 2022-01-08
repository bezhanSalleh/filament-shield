<?php

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class FilamentShield
{
    public static function generateForResource(string $resource): void
    {
        $permissions = collect();
        collect(config('filament-shield.resource_permission_prefixes'))
            ->each(function ($prefix) use ($resource, $permissions) {
                $permissions->push(Permission::firstOrCreate(
                    ['name' => $prefix.'_'.Str::lower($resource)],
                    ['guard_name' => config('filament.auth.guard')]
                ));
            });

        static::giveSuperAdminPermission($permissions);
    }

    public static function generateForPage(string $page): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => config('filament-shield.page_permission_prefix').'_'.Str::lower($page)],
            ['guard_name' => config('filament.auth.guard')]
        )->name;

        static::giveSuperAdminPermission($permission);
    }

    public static function generateForWidget(string $widget): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => config('filament-shield.widget_permission_prefix').'_'.Str::lower($widget)],
            ['guard_name' => config('filament.auth.guard')]
        )->name;

        static::giveSuperAdminPermission($permission);
    }

    protected static function giveSuperAdminPermission(string|array|Collection $permissions): void
    {
        $superAdmin = Role::firstOrCreate(
            ['name' => config('filament-shield.default_roles.super_admin_role_name')],
            ['guard_name' => config('filament.auth.guard')]
        );

        $superAdmin->givePermissionTo($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
