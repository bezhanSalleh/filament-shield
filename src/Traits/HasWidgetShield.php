<?php

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

trait HasWidgetShield
{
    public static function canView(): bool
    {
        return Filament::auth()->user()->can(static::getPermissionName()) || Filament::auth()->user()->hasRole(Utils::getSuperAdminName());
    }

    protected static function getPermissionName(): string
    {
        return Str::of(class_basename(static::class))
            ->prepend(
                Str::of(Utils::getWidgetPermissionPrefix())
                    ->append('_')
                    ->toString()
            )
            ->toString();
    }
}
