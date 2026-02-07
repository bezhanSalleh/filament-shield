<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

trait HasPageShield
{
    protected static ?string $pagePermissionKey = null;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess() && parent::shouldRegisterNavigation();
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        $permissions = static::getPagePermissions();

        if (empty($permissions)) {
            return parent::canAccess();
        }

        // Logik: Hat der User MINDESTENS EINES der Rechte für diese Seite?
        return $user->hasAnyPermission($permissions);
    }

    /**
     * Automatically creates an array of all page permissions as Booleans.
     * Ideal for passing to child components.
     */
    public function getShieldPermissions(): array
    {
        // Wir nutzen die Methode, die du bereits in deiner Page definiert hast
        if (! method_exists($this, 'getShieldPagePermissions')) {
            return [];
        }

        return collect(static::getShieldPagePermissions())
            ->mapWithKeys(fn (string $action) => [
                $action => $this->canShield($action),
            ])
            ->toArray();
    }

    /**
     * Prüft eine feingranulare Berechtigung für die aktuelle Page.
     */
    public function canShield(string $action): bool
    {
        $prefix = config('filament-shield.pages.permission_prefix', 'Page');
        $separator = config('filament-shield.permissions.separator', ':');
        $case = config('filament-shield.permissions.case', 'pascal');

        $permission = collect([
            Str::of($prefix)->studly()->toString(),
            Str::of($action)->studly()->toString(),
            class_basename(static::class),
        ])->join($separator);

        return Filament::auth()->user()?->can($permission) ?? false;
    }

    protected static function getPagePermissions(): array
    {
        $page = FilamentShield::getPages()[static::class] ?? null;

        if (! $page) {
            return [];
        }

        // Wir extrahieren alle Keys (z.B. ['Page:View:X', 'Page:Edit:X'])
        return collect($page['permissions'])
            ->pluck('key')
            ->toArray();
    }
}
