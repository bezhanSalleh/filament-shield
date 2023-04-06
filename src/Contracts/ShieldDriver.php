<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ShieldDriver
{
    public function createRole(array $data): Model;

    public function createPermission(array $data): Model;

    public function hasRole($user, string $role): bool;

    public function hasPermission($user, string $permission): bool;

    public function syncPermissions($user, array $permissions): void;

    public function syncRoles($user, array $roles): void;
}
