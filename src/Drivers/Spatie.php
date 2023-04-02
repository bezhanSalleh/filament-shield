<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Drivers;

use BezhanSalleh\FilamentShield\Contracts\ShieldDriver;

class Spatie implements ShieldDriver
{
    public function hasRole($user, string $role): bool
    {
        return $user->hasRole($role);
    }

    public function hasPermission($user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }

    public function syncPermissions($user, array $permissions): void
    {
        $user->syncPermissions($permissions);
    }

    public function syncRoles($user, array $roles): void
    {
        $user->syncRoles($roles);
    }
}
