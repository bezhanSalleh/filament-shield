<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Contracts;

interface ShieldDriver
{
    public function hasRole($user, string $role): bool;

    public function hasPermission($user, string $permission): bool;

    public function syncPermissions($user, array $permissions): void;

    public function syncRoles($user, array $roles): void;
}
