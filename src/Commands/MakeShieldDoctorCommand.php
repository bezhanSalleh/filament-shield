<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Support\Utils;
use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Foundation\Console\AboutCommand;

class MakeShieldDoctorCommand extends Command
{
    public $signature = 'shield:doctor';

    public $description = 'Show useful info about Filament Shield';

    public function handle(): int
    {
        AboutCommand::add('Filament Shield', [
            'Auth Provider' => Utils::getAuthProviderFQCN() . '|' . static::authProviderConfigured(),
            'Resource' => Utils::isResourcePublished() ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            'Resource Slug' => Utils::getResourceSlug(),
            'Resource Sort' => Utils::getResourceNavigationSort(),
            'Resource Badge' => Utils::isResourceNavigationBadgeEnabled() ? '<fg=green;options=bold>ENABLED</>' : '<fg=red;options=bold>DISABLED</>',
            'Resource Group' => Utils::isResourceNavigationGroupEnabled() ? '<fg=green;options=bold>ENABLED</>' : '<fg=red;options=bold>DISABLED</>',
            'Translations' => is_dir(resource_path('resource/lang/vendor/filament-shield')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            'Views' => is_dir(resource_path('views/vendor/filament-shield')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            'Version' => InstalledVersions::getPrettyVersion('bezhansalleh/filament-shield'),
        ]);

        $this->call('about', [
            '--only' => 'filament_shield',
        ]);

        return self::SUCCESS;
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
