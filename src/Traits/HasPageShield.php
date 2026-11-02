<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Facades\Filament;

trait HasPageShield
{
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess() && parent::shouldRegisterNavigation();
    }

    public static function canAccess(): bool
    {
        $permission = static::getPagePermission();
        $user = Filament::auth()?->user();

        return $permission && $user
            ? $user->can($permission)
            : parent::canAccess();
    }

    protected static function getPagePermission(): ?string
    {
        return array_key_first(FilamentShield::getPages()[static::class]['permissions'] ?? []);
    }
}
