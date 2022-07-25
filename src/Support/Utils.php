<?php

namespace BezhanSalleh\FilamentShield\Support;

use Illuminate\Support\Facades\Schema;

class Utils
{
    public static function getResourceSlug(): string
    {
        return (string) config('filament-shield.shield_resource.slug');
    }

    public static function getResourceNavigationSort(): int
    {
        return config('filament-shield.shield_resource.navigation_sort');
    }

    public static function isSettingPageEnabled(): bool
    {
        return (bool) config('filament-shield.settings.enabled');
    }

    public static function isSettingPageConfigured(): bool
    {
        return static::isSettingPageEnabled() && Schema::hasTable('filament_shield_settings');
    }

    public static function getAuthProviderFQCN()
    {
        return config('filament-shield.auth_provider_model.fqcn');
    }

    public static function isAuthProviderConfigured(): bool
    {
        return in_array("BezhanSalleh\FilamentShield\Traits\HasFilamentShield", class_uses(static::getAuthProviderFQCN()))
        || in_array("Spatie\Permission\Traits\HasRoles", class_uses(static::getAuthProviderFQCN())) ;
    }
}
