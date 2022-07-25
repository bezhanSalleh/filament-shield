<?php

namespace BezhanSalleh\FilamentShield\Support;

use Illuminate\Support\Facades\Schema;

class Utils
{
    public static function getResourceSlug(): string
    {
        return config('filament-shield.shield_resource.slug');
    }

    public static function getResourceNavigationSort(): int
    {
        return config('filament-shield.shield_resource.navigation_sort');
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
        return static::isSettingPageEnabled()
            ? Schema::hasTable('filament_shield_settings')
            : false;
    }

    public static function getAuthProviderFQCN(): string
    {
        return config('filament-shield.auth_provider_model.fqcn');
    }
}
