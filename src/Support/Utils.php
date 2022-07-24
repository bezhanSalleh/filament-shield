<?php

namespace BezhanSalleh\FilamentShield\Support;

class Utils
{
    public static function isResourceEnabled(): bool
    {
        return config('filament-shield.shield_resource.enabled');
    }

    public static function getResourceClass(): string
    {
        return config('filament-shield.shield_resource.resource');
    }

    public static function getResourceSlug(): string
    {
        return static::isResourceEnabled()
            ? config('filament-shield.shield_resource.slug')
            : '';
    }

    public static function getResourceNavigationSort(): int
    {
        return static::isResourceEnabled()
            ? config('filament-shield.shield_resource.navigation_sort')
            : '' ;
    }
}
