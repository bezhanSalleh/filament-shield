<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use BezhanSalleh\FilamentShield\Support\Utils;
use Composer\InstalledVersions;
use Filament\Facades\Filament;
use Illuminate\Foundation\Console\AboutCommand;

trait HasAboutCommand
{
    public function initAboutCommand()
    {
        AboutCommand::add('Shield', [
            'Auth Provider' => Utils::getAuthProviderFQCN() . '|' . static::authProviderConfigured(),
            // 'Resource' => Utils::isResourcePublished(Filament::getCurrentPanel()) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            // 'Resource Slug' => Utils::getResourceSlug(),
            // 'Resource Sort' => Utils::getResourceNavigationSort(),
            // 'Resource Badge' => Utils::isResourceNavigationBadgeEnabled() ? '<fg=green;options=bold>ENABLED</>' : '<fg=red;options=bold>DISABLED</>',
            // 'Resource Group' => Utils::isResourceNavigationGroupEnabled() ? '<fg=green;options=bold>ENABLED</>' : '<fg=red;options=bold>DISABLED</>',
            'Tenancy' => Utils::isTenancyEnabled() ? '<fg=green;options=bold>ENABLED</>' : '<fg=gray;options=bold>DISABLED</>',
            'Tenant Model' => Utils::isTenancyEnabled() && filled($model = config()->get('filament-shield.tenant_model')) ? $model : null,
            'Translations' => is_dir(resource_path('resource/lang/vendor/filament-shield')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            'Views' => is_dir(resource_path('views/vendor/filament-shield')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            'Version' => InstalledVersions::getPrettyVersion('bezhansalleh/filament-shield'),
        ]);
    }

    protected static function authProviderConfigured(): string
    {
        if (class_exists(Utils::getAuthProviderFQCN())) {
            return Utils::isAuthProviderConfigured()
                ? '<fg=green;options=bold>CONFIGURED</>'
                : '<fg=red;options=bold>NOT CONFIGURED</>';
        }

        return '';
    }
}
