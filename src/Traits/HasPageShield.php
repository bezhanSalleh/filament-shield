<?php

namespace BezhanSalleh\FilamentShield\Traits;

use Illuminate\Support\Str;
use Filament\Facades\Filament;

trait HasPageShield
{

    public function mount()
    {
        if (! static::canView()) {
            $this->notify('warning',__('filament-shield::filament-shield.forbidden'));
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
            ->snake()
            ->prepend(config('filament-shield.page_permission_prefix').'_');
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return static::canView();
    }
}
