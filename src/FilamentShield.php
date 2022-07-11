<?php

namespace BezhanSalleh\FilamentShield;

use Filament\Facades\Filament;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FilamentShield
{
    public static function generateForResource(string $resource): void
    {
        if (config('filament-shield.entities.resources')) {
            $permissions = collect();
            collect(config('filament-shield.prefixes.resource'))
                ->each(function ($prefix) use ($resource, $permissions) {
                    $permissions->push(Permission::firstOrCreate(
                        ['name' => $prefix . '_' . Str::lower($resource)],
                        ['guard_name' => config('filament.auth.guard')]
                    ));
                });

            static::giveSuperAdminPermission($permissions);
            static::giveFilamentUserPermission($permissions);
        }
    }

    public static function generateForPage(string $page): void
    {
        if (config('filament-shield.entities.pages')) {
            $permission = Permission::firstOrCreate(
                ['name' => config('filament-shield.prefixes.page') . '_' . Str::lower($page)],
                ['guard_name' => config('filament.auth.guard')]
            )->name;

            static::giveSuperAdminPermission($permission);
            static::giveFilamentUserPermission($permission);
        }
    }

    public static function generateForWidget(string $widget): void
    {
        if (config('filament-shield.entities.widgets')) {
            $permission = Permission::firstOrCreate(
                ['name' => config('filament-shield.prefixes.widget') . '_' . Str::lower($widget)],
                ['guard_name' => config('filament.auth.guard')]
            )->name;

            static::giveSuperAdminPermission($permission);
            static::giveFilamentUserPermission($permission);
        }
    }

    protected static function giveSuperAdminPermission(string|array|Collection $permissions): void
    {
        if (config('filament-shield.super_admin.enabled')) {
            $superAdmin = Role::firstOrCreate(
                ['name' => config('filament-shield.super_admin.role_name')],
                ['guard_name' => config('filament.auth.guard')]
            );

            $superAdmin->givePermissionTo($permissions);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    protected static function giveFilamentUserPermission(string|array|Collection $permissions): void
    {
        if (config('filament-shield.filament_user.enabled')) {
            $filamentUser = Role::firstOrCreate(
                ['name' => config('filament-shield.filament_user.role_name')],
                ['guard_name' => config('filament.auth.guard')]
            );

            $filamentUser->givePermissionTo($permissions);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    /**
     * Return Resources as key value paire of Entities
     *
     * @return void
     */
    public static function getEntities()
    {
        return collect(Filament::getResources())
            ->unique()
            ->filter(function ($resource) {
                if (config('filament-shield.exclude.enabled')) {
                    return ! in_array(
                        Str::of($resource)->afterLast('\\'),
                        config('filament-shield.exclude.resources')
                    );
                }

                return true;
            })
            ->reduce(function ($roles, $resource) {
                $role = str($resource)->afterLast('\\')->before('Resource')->lower()->toString();
                $roles[$role] = $role;

                return $roles;
            }, []);
    }

    public static function getEntityLabel(string $entity): String
    {
        $label = collect(Filament::getResources())
                ->filter(function ($resource) use ($entity) {
                    return str($resource)->endsWith(str($entity)->ucfirst().'Resource');
                })
                ->first()::getModelLabel();

        return str($label)->headline();
    }

    /**
     * Shield structured data.
     *
     * @return array
     */
    public static function getShieldData(): array
    {
        return collect(static::getEntities())
            ->map(function ($entity) {
                return collect(config('filament-shield.prefixes.resource'))
                    ->reduce(
                        function ($option, $permission) use ($entity) {
                            $option[$permission . '_' . $entity] = ['label' => $permission,'value' => false];

                            return $option;
                        },
                        [
                            'label' => static::getEntityLabel($entity),
                            'value' => false,
                        ]
                    );
            })
            ->sortKeys()
            ->all();
    }
}
