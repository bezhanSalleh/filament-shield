<?php

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Str;
use Composer\InstalledVersions;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use BezhanSalleh\FilamentShield\Models\Setting;
use Illuminate\Foundation\Console\AboutCommand;

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

        /** @phpstan-ignore-next-line */
        if (Schema::hasTable('filament_shield_settings')) {
            /** @phpstan-ignore-next-line */
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
    }

    protected function getCommands(): array
    {
        return [
            Commands\MakeShieldDoctorCommand::class,
            Commands\MakeCreateShieldCommand::class,
            Commands\MakeInstallShieldCommand::class,
            Commands\MakeGenerateShieldCommand::class,
            Commands\MakeSuperAdminShieldCommand::class,
        ];
    }

    protected static function authProviderConfigured()
    {
        return in_array("BezhanSalleh\FilamentShield\Traits\HasFilamentShield", class_uses(config('filament-shield.auth_provider_model.fqcn')))
         || in_array("Spatie\Permission\Traits\HasRoles", class_uses(config('filament-shield.auth_provider_model.fqcn')))
            ? '<fg=green;options=bold>YES</>'
            : '<fg=red;options=bold>NO</>' ;
    }
}
