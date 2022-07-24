<?php

namespace BezhanSalleh\FilamentShield\Support;

use Illuminate\Support\Facades\Schema;

class Utils
{
    public static function isResourceEnabled(): bool
    {
        return config('filament-shield.shield_resource.enabled') ?? false;
    }

    public static function getResourceClass(): string
    {
        return config('filament-shield.shield_resource.resource');
    }

    public static function usesBuiltInResource()
    {
        return static::getResourceClass() === 'BezhanSalleh\\FilamentShield\\Resources\\RoleResource';
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

    public static function isSettingPageEnabled()
    {
        return config('filament-shield.settings.enabled') ?? false;
    }

    public static function getSettingPageClass()
    {
        return \BezhanSalleh\FilamentShield\Pages\ShieldSettings::class;
    }

    public static function isSettingPageConfigured()
    {
        return Schema::hasTable('filament_shield_settings');
    }

    public static function getAuthProviderFQCN(): string
    {
        return config('filament-shield.auth_provider_model.fqcn');
    }
}
