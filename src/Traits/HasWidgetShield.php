<?php

namespace BezhanSalleh\FilamentShield\Traits;

use Filament\Facades\Filament;
use Illuminate\Support\Str;

trait HasWidgetShield
{
    public static function canView(): bool
    {
        return Filament::auth()->user()->can(static::getPermissionName());
    }

    protected static function getPermissionName(): string
    {
        return (string) Str::of(static::class)
            ->after('Widgets\\')
            ->replace('\\', '')
            ->snake()
            ->prepend(config('filament-shield.prefixes.widget').'_');
    }
}
