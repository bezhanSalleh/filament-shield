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
        return array_key_first(FilamentShield::getWidgets()[static::class]['permissions'] ?? []);
    }
}
