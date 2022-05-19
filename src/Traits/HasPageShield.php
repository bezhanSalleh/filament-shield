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
        return Filament::auth()->user()->can(static::getPermissionName());
    }

    protected static function getPermissionName(): string
    {
        return (string) Str::of(static::class)
            ->after('Pages\\')
            ->replace('\\', '')
            ->snake()
            ->prepend(config('filament-shield.prefixes.page').'_');
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return static::canView() && static::$shouldRegisterNavigation;
    }
}
