<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Traits;

use Illuminate\Support\Str;
use Filament\Facades\Filament;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;

trait HasWidgetShield
{
    protected static ?string $widgetPermissionKey = null;

    public static function canView(): bool
    {
        $permission = static::getWidgetPermission();
        $user = Filament::auth()?->user();

        return $permission && $user
            ? $user->can($permission)
            : parent::canAccess();
    }

    protected static function getWidgetPermission(): ?string
    {
        if (static::$widgetPermissionKey === null) {
            $widget = FilamentShield::getWidgets()[static::class] ?? null;
            static::$widgetPermissionKey = $widget ? array_key_first($widget['permissions']) : null;
        }

        return static::$widgetPermissionKey;
    }
}
