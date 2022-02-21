<?php

namespace BezhanSalleh\FilamentShield\Traits;

use Filament\Facades\Filament;
use Illuminate\Support\Str;

trait HasPageShield
{
    public function mount()
    {
        if (! static::canView()) {
            $this->notify('warning', __('filament-shield::filament-shield.forbidden'));

            return redirect(config('filament.path'));
        }
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
