<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Contracts;

interface HasShieldPermissions
{
    public static function getPermissionPrefixes(): array;
}
