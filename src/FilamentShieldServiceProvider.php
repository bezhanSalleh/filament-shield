<?php

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Models\Setting;
use BezhanSalleh\FilamentShield\Pages\ShieldSetting;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;

class FilamentShieldServiceProvider extends PluginServiceProvider
{
    protected array $pages = [
        ShieldSetting::class,
    ];

    protected array $resources = [
        RoleResource::class,
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

        if (config('filament-shield.register_role_policy.enabled')) {
            Gate::policy('Spatie\Permission\Models\Role', 'App\Policies\RolePolicy');
        }

        /** @phpstan-ignore-next-line */
        if (Utils::isSettingPageEnabled()) {
            config(['filament-shield' => Setting::pluck('value', 'key')->toArray()]);
        }
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-shield', function (): FilamentShield {
            return new FilamentShield();
        });

        $this->publishes([
            $this->package->basePath("/../stubs/ShieldSettingSeeder.stub") => database_path('seeders/ShieldSettingSeeder.php'),
        ], "{$this->package->shortName()}-seeder");
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
}
