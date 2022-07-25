<?php

namespace BezhanSalleh\FilamentShield\Traits;

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
        return;
    }

    protected function afterBooted(): void
    {
        return;
    }

    protected function beforeShieldRedirects(): void
    {
        return;
    }

    protected function getShieldRedirectPath(): string
    {
        return config('filament.path');
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()->can(static::getPermissionName()) || Filament::auth()->user()->hasRole(config('filament-shield.super_admin.name'));
    }

    protected static function getPermissionName(): string
    {
        $prepend = Str::of(config('filament-shield.permission_prefixes.page'))->append('_');

        return Str::of(class_basename(static::class))
            ->prepend($prepend);
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return static::canView() && static::$shouldRegisterNavigation;
    }
}
