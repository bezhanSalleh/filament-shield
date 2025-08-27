<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Concerns\CanBeCentralApp;
use BezhanSalleh\FilamentShield\Concerns\CanCustomizeColumns;
use BezhanSalleh\FilamentShield\Concerns\CanLocalizePermissionLabels;
use BezhanSalleh\FilamentShield\Concerns\HasSimpleResourcePermissionView;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\PluginEssentials\Concerns\Plugin as Essentials;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentShieldPlugin implements Plugin
{
    use CanBeCentralApp;
    use CanCustomizeColumns;
    use CanLocalizePermissionLabels;
    use Essentials\BelongsToParent;
    use Essentials\BelongsToTenant;
    use Essentials\HasGlobalSearch;
    use Essentials\HasLabels;
    use Essentials\HasNavigation;
    use Essentials\HasPluginDefaults;
    use EvaluatesClosures;
    use HasSimpleResourcePermissionView;

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
            'modelLabel' => __('filament-shield::filament-shield.resource.label.role'),
            'pluralModelLabel' => __('filament-shield::filament-shield.resource.label.roles'),

            'navigationGroup' => __('filament-shield::filament-shield.nav.group'),
            'navigationLabel' => __('filament-shield::filament-shield.nav.role.label'),
            'navigationIcon' => __('filament-shield::filament-shield.nav.role.icon'),
            'activeNavigationIcon' => 'heroicon-s-shield-check',
        ];
    }
}
