<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Contracts;

interface HasShieldPermissions
{
    /**
     * @deprecated version 3.x Use `filament-shield.resources.manage` instead to define resource specific permissions.
     */
    public static function getPermissionPrefixes(): array;
}
