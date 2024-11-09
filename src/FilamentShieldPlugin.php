<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentShieldPlugin implements Plugin
{
    use Concerns\CanBeCentralApp;
    use Concerns\CanCustomizeColumns;
    use Concerns\CanLocalizePermissionLabels;
    use Concerns\HasSimpleResourcePermissionView;
    use EvaluatesClosures;

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

        if (! Utils::isResourcePublished($panel)) {
            $panel->resources([
                Resources\RoleResource::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
