<?php

namespace BezhanSalleh\FilamentShield\Support;

class Utils
{
    public static function isResourceEnabled(): bool
    {
        config(['filament-shield.shield_resource.enabled' => true]);
        return config('filament-shield.shield_resource.enabled');
    }

    public static function getResourceClass(): string
    {
        config(['filament-shield.shield_resource.resource' => \BezhanSalleh\FilamentShield\Resources\RoleResource::class]);

        return config('filament-shield.shield_resource.resource');
    }

    public static function getResourceSlug(): string
    {
        config(['filament-shield.shield_resource.slug' => 'shield/roles']);

        return config('filament-shield.shield_resource.slug');
    }

    public static function getResourceNavigationSort(): int
    {
        config(['filament-shield.shield_resource.navigation_sort' => -1]);

        return config('filament-shield.shield_resource.navigation_sort');
    }
}
