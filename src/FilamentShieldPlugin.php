<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;

class FilamentShieldPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-shield';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            Resources\RoleResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        if (Utils::isSuperAdminDefinedViaGate()) {
            Gate::{Utils::getSuperAdminGateInterceptionStatus()}(function ($user, $ability) {
                return match (Utils::getSuperAdminGateInterceptionStatus()) {
                    'before' => $user->hasRole(Utils::getSuperAdminName()) ? true : null,
                    'after' => $user->hasRole(Utils::getSuperAdminName()),
                    default => false
                };
            });
        }

        if (Utils::isRolePolicyRegistered()) {
            Gate::policy(Utils::getRoleModel(), 'App\Policies\RolePolicy');
        }
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}
