<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Panel;

trait HasPanelShield
{
    public static function bootHasPanelShield(): void
    {
        if (! app()->runningInConsole() && Utils::isPanelUserRoleEnabled()) {
            Utils::createPanelUserRole();
            static::created(fn ($user) => $user->assignRole(Utils::getPanelUserRoleName()));
            static::deleting(fn ($user) => $user->removeRole(Utils::getPanelUserRoleName()));
        }
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole(Utils::getSuperAdminName())) {
            return true;
        }

        return (bool) $this->hasRole(Utils::getPanelUserRoleName());
    }
}
