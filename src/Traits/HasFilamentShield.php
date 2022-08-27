<?php

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\Traits\HasRoles;

trait HasFilamentShield
{
    use HasRoles;

    public static function bootHasFilamentShield()
    {
        if (Utils::isFilamentUserRoleEnabled()) {
            static::created(fn ($user) => $user->assignRole(Utils::getFilamentUserRoleName()));

            static::deleting(fn ($user) => $user->removeRole(Utils::getFilamentUserRoleName()));
        }
    }

    public function canAccessFilament(): bool
    {
        return $this->hasRole(Utils::getSuperAdminName()) || $this->hasRole(Utils::getFilamentUserRoleName());
    }
}
