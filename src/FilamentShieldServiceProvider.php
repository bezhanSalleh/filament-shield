<?php

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Arr;
use Composer\InstalledVersions;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Models\Setting;
use Illuminate\Foundation\Console\AboutCommand;

class FilamentShieldServiceProvider extends PluginServiceProvider
{
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

        if (config('filament-shield.settings.driver') === 'database') {
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
                'Auth Provider' => config('filament-shield.auth_provider_model.fqcn'),
                'Auth Provider Configured' => static::authProviderConfigured(),
                'Settings Driver' => config('filament-shield.settings.driver'),
                'Settings GUI' => config('filament-shield.settings.gui_enabled')
                    ? '<fg=green;options=bold>ENABLED</>'
                    : '<fg=red;options=bold>DISABLED</>',
                'Translations' => is_dir(resource_path('resource/lang/vendor/filament-shield')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
                'Version' => InstalledVersions::getPrettyVersion('bezhansalleh/filament-shield'),
                'Views' => is_dir(resource_path('views/vendor/filament-shield')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            ]);
        }

        // $this->mergeConfigFrom(__DIR__ . '/../config/filament-shield.php', 'filament-shield');

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

    protected function getResources(): array
    {
        return [
            Utils::isResourceEnabled() ? Utils::getResourceClass() : '',
        ];
    }

    protected static function authProviderConfigured()
    {
        if (class_exists(config('filament-shield.auth_provider_model.fqcn'))) {
            return in_array("BezhanSalleh\FilamentShield\Traits\HasFilamentShield", class_uses(config('filament-shield.auth_provider_model.fqcn')))
            || in_array("Spatie\Permission\Traits\HasRoles", class_uses(config('filament-shield.auth_provider_model.fqcn')))
                ? '<fg=green;options=bold>YES</>'
                : '<fg=red;options=bold>NO</>' ;
        }

        return '';
    }

    // protected function mergeConfig(array $original, array $merging): array
    // {
    //     $array = array_merge($original, $merging);

    //     foreach ($original as $key => $value) {
    //         if (! is_array($value)) {
    //             continue;
    //         }

    //         if (! Arr::exists($merging, $key)) {
    //             continue;
    //         }

    //         if (is_numeric($key)) {
    //             continue;
    //         }

    //         if ($key === 'middleware' || $key === 'register') {
    //             continue;
    //         }

    //         $array[$key] = $this->mergeConfig($value, $merging[$key]);
    //     }

    //     return $array;
    // }

    // protected function mergeConfigFrom($path, $key): void
    // {
    //     $config = $this->app['config']->get($key, []);

    //     $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    // }
}
