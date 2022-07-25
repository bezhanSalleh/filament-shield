<?php

namespace BezhanSalleh\FilamentShield\Traits;

use Filament\Facades\Filament;
use Illuminate\Support\Str;

trait HasWidgetShield
{
    public static function canView(): bool
    {
        return Filament::auth()->user()->can(static::getPermissionName()) || Filament::auth()->user()->hasRole(config('filament-shield.super_admin.name'));
    }

    protected static function getPermissionName(): string
    {
        $prepend = Str::of(config('filament-shield.permission_prefixes.widget'))->append('_');

        return Str::of(class_basename(static::class))
            ->prepend($prepend);
    }
}
