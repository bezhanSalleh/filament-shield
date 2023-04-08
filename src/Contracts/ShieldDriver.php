<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ShieldDriver
{
    public function createRole(array $data): Model;

    public function hasRole($user, string $role): bool;

    public function syncRoles($user, string|array $roles): void;

    public function createPermission(array $data): Model;

    public function hasPermission($user, string $permission): bool;

    public function syncPermissions($user, string|array $permissions): void;

    public function givePermissionsToRole(Model $role, mixed $permissions): void;
}
