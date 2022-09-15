<?php

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

trait HasPageShield
{
    public function booted(): void
    {
        $this->beforeBooted();

        if (! static::canView()) {
            $this->notify('warning', __('filament-shield::filament-shield.forbidden'));

            $this->beforeShieldRedirects();

            redirect($this->getShieldRedirectPath());

            return;
        }

        if (method_exists(parent::class, 'booted')) {
            parent::booted();
        }

        $this->afterBooted();
    }

    protected function beforeBooted(): void
    {
    }

    protected function afterBooted(): void
    {
    }

    protected function beforeShieldRedirects(): void
    {
    }

    protected function getShieldRedirectPath(): string
    {
        return config('filament.path');
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()->can(static::getPermissionName()) || Filament::auth()->user()->hasRole(Utils::getSuperAdminName());
    }

    protected static function getPermissionName(): string
    {
        $prepend = Str::of(Utils::getPagePermissionPrefix())->append('_');

        return Str::of(class_basename(static::class))
            ->prepend($prepend);
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return static::canView() && static::$shouldRegisterNavigation;
    }
}
