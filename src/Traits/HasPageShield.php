<?php

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

trait HasPageShield
{
    public function booted(): void
    {
        $this->beforeBooted();

        if (! static::canAccess()) {

            Notification::make()
                ->title(__('filament-shield::filament-shield.forbidden'))
                ->warning()
                ->send();

            $this->beforeShieldRedirects();

            redirect($this->getShieldRedirectPath());

            return;
        }

        if (method_exists(parent::class, 'booted')) {
            parent::booted();
        }

        $this->afterBooted();
    }

    protected function beforeBooted(): void {}

    protected function afterBooted(): void {}

    protected function beforeShieldRedirects(): void {}

    protected function getShieldRedirectPath(): string
    {
        return Filament::getUrl();
    }

    protected static function getPermissionName(): string
    {
        return Str::of(class_basename(static::class))
            ->prepend(
                Str::of(Utils::getPagePermissionPrefix())
                    ->append('_')
                    ->toString()
            )
            ->toString();
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::canAccess() && parent::shouldRegisterNavigation();
    }

    public static function canAccess(array $parameters = []): bool
    {
        return Filament::auth()->user()->can(static::getPermissionName());
    }
}
