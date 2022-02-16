<?php

namespace BezhanSalleh\FilamentShield\Traits;

use Spatie\Permission\Traits\HasRoles;

trait HasFilamentShield
{
    use HasRoles;

    public static function bootHasFilamentShield()
    {
        if (config('filament-shield.filament_user.enabled')) {
            static::created(fn ($user) => $user->assignRole(static::filamentUserRole()));

            static::deleting(fn ($user) => $user->removeRole(static::filamentUserRole()));
        }
    }

    public function canAccessFilament(): bool
    {
        return $this->hasRole(static::filamentUserRole());
    }

    protected static function filamentUserRole(): string
    {
        return (string) config('filament-shield.filament_user.role_name');
    }
}
