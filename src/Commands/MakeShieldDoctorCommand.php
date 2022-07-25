<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Foundation\Console\AboutCommand;

class MakeShieldDoctorCommand extends Command
{
    public $signature = 'shield:doctor';

    public $description = 'Show usefull info about Filament Shield';

    public function handle(): int
    {
        if (class_exists(AboutCommand::class)) {
            AboutCommand::add('Filament Shield', [
                'Auth Provider' => Utils::getAuthProviderFQCN().'|'.static::authProviderConfigured(),
                'Resource Slug' => Utils::getResourceSlug(),
                'Resource Sort' => Utils::getResourceNavigationSort(),
                'Setting Page' => Utils::isSettingPageEnabled() ? '<fg=green;options=bold>ENABLED</>' .(Utils::isSettingPageConfigured() ? '|<fg=green;options=bold>CONFIGURED</>' : '|<fg=red;options=bold>NOT CONFIGURED</>') : '<fg=red;options=bold>DISABLED</>' .(Utils::isSettingPageConfigured() ? '|<fg=green;options=bold>CONFIGURED</>' : '|<fg=red;options=bold>NOT CONFIGURED</>'),
                'Translations' => is_dir(resource_path('resource/lang/vendor/filament-shield')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
                'Version' => InstalledVersions::getPrettyVersion('bezhansalleh/filament-shield'),
                'Views' => is_dir(resource_path('views/vendor/filament-shield')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            ]);
        }

        $this->call('about', [
            '--only' => 'filament_shield',
        ]);

        return self::SUCCESS;
    }

    protected static function authProviderConfigured()
    {
        if (class_exists(Utils::getAuthProviderFQCN())) {
            return in_array("BezhanSalleh\FilamentShield\Traits\HasFilamentShield", class_uses(Utils::getAuthProviderFQCN()))
            || in_array("Spatie\Permission\Traits\HasRoles", class_uses(Utils::getAuthProviderFQCN()))
                ? '<fg=green;options=bold>CONFIGURED</>'
                : '<fg=red;options=bold>NOT CONFIGURED</>' ;
        }

        return '';
    }
}
