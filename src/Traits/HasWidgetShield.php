<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Facades\Filament;

trait HasWidgetShield
{
    public static function canView(): bool
    {
        $permission = static::getWidgetPermission();
        $user = Filament::auth()?->user();

        return $permission && $user
            ? $user->can($permission)
            : parent::canView();
    }

    protected static function getWidgetPermission(): ?string
    {
        // Same subclass-static pitfall as HasPageShield — getWidgets() is
        // already memoised with once().
        $widget = FilamentShield::getWidgets()[static::class] ?? null;

        return $widget ? array_key_first($widget['permissions']) : null;
    }
}
