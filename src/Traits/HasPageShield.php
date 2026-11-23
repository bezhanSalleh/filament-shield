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
        // Do not cache on a trait static — subclasses that share an abstract
        // parent would reuse the first resolved permission key. getPages() is
        // already memoised with once().
        $page = FilamentShield::getPages()[static::class] ?? null;

        return $page ? array_key_first($page['permissions']) : null;
    }
}
