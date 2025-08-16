<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use Filament\Panel;
use Filament\Contracts\Plugin;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Support\Concerns\EvaluatesClosures;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Concerns\CanBeCentralApp;
use BezhanSalleh\FilamentShield\Concerns\CanCustomizeColumns;
use BezhanSalleh\PluginEssentials\Concerns\Plugin as Essentials;
use BezhanSalleh\FilamentShield\Concerns\CanLocalizePermissionLabels;
use BezhanSalleh\FilamentShield\Concerns\HasSimpleResourcePermissionView;

class FilamentShieldPlugin implements Plugin
{
    use CanBeCentralApp;
    use CanCustomizeColumns;
    use CanLocalizePermissionLabels;
    use EvaluatesClosures;
    use HasSimpleResourcePermissionView;
    use Essentials\HasNavigation;
    use Essentials\HasLabels;
    use Essentials\HasGlobalSearch;
    use Essentials\BelongsToCluster;
    use Essentials\BelongsToParent;
    use Essentials\BelongsToTenant;
    use Essentials\HasPluginDefaults;

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
                RoleResource::class,
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

    protected function getPluginDefaults(): array
    {
        return [
            'navigationGroup' => 'Administration',
            'navigationLabel' => 'Roles',
            'navigationIcon' => 'heroicon-o-shield-check',
            'activeNavigationIcon' => 'heroicon-s-shield-check',
        ];
    }
}
