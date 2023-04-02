<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Drivers;

use BezhanSalleh\FilamentShield\Contracts\ShieldDriver;

class Bouncer implements ShieldDriver
{
    public function hasRole($user, string $role): bool
    {
        return $user->is($role);
    }

    public function hasPermission($user, string $permission): bool
    {
        return $user->can($permission);
    }

    public function syncPermissions($user, array $permissions): void
    {
        $user->abilities->each(function ($ability) use ($user) {
            $user->disallow($ability->name);
        });

        foreach ($permissions as $permission) {
            $user->allow($permission);
        }
    }

    public function syncRoles($user, array $roles): void
    {
        $user->roles->each(function ($role) use ($user) {
            $user->retract($role->name);
        });

        foreach ($roles as $role) {
            $user->assign($role);
        }
    }
}
