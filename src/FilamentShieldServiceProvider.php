<?php

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Models\Setting;
use BezhanSalleh\FilamentShield\Support\Utils;
use Composer\InstalledVersions;
use Filament\PluginServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use Spatie\LaravelPackageTools\Package;

class FilamentShieldServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
        \BezhanSalleh\FilamentShield\Resources\RoleResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-shield')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasCommands($this->getCommands())
            ->hasMigration('create_filament_shield_settings_table')
        ;
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        if (Utils::isSettingPageEnabled()) {
            config(['filament-shield' => Setting::pluck('value', 'key')->toArray()], '');
        }

        if (config('filament-shield.register_role_policy.enabled')) {
            \Illuminate\Support\Facades\Gate::policy('Spatie\Permission\Models\Role', 'App\Policies\RolePolicy');
        }
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-shield', function (): \BezhanSalleh\FilamentShield\FilamentShield {
            return new \BezhanSalleh\FilamentShield\FilamentShield();
        });

        $this->publishes([
            $this->package->basePath("/../stubs/ShieldSettingSeeder.stub") => database_path('seeders/ShieldSettingSeeder.php'),
        ], "{$this->package->shortName()}-seeder");

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
    }

    protected function getCommands(): array
    {
        return [
            Commands\MakeShieldDoctorCommand::class,
            Commands\MakeShieldInstallCommand::class,
            Commands\MakeShieldGenerateCommand::class,
            Commands\MakeShieldSuperAdminCommand::class,
        ];
    }

    protected function getPages(): array
    {
        if (Utils::isSettingPageEnabled()) {
            return [
                Utils::getSettingPageClass(),
            ];
        }

        return [];
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
