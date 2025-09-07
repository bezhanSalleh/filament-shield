<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Concerns\Plugin;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\PluginEssentials\Concerns\Plugin as Essentials;
use Filament\Contracts\Plugin as FilamentPlugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentShieldPlugin implements FilamentPlugin
{
    use Essentials\BelongsToParent;
    use Essentials\BelongsToTenant;
    use Essentials\HasGlobalSearch;
    use Essentials\HasLabels;
    use Essentials\HasNavigation;
    use Essentials\HasPluginDefaults;
    use EvaluatesClosures;
    use Plugin\CanBeCentralApp;
    use Plugin\CanCustomizeColumns;
    use Plugin\CanLocalizePermissionLabels;
    use Plugin\HasSimpleResourcePermissionView;

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
