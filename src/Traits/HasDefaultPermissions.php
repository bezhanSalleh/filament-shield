<?php

namespace BezhanSalleh\FilamentShield\Traits;

trait HasDefaultPermissions
{
    public static function permissions(): array
    {
        return config('filament-shield.prefixes.resource');
    }
}
