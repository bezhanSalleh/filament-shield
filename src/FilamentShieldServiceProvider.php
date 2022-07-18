<?php

namespace BezhanSalleh\FilamentShield;

use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Models\Setting;

class FilamentShieldServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-shield')
            ->hasViews()
            ->hasTranslations()
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
    }

    protected function getCommands(): array
    {
        return [
            Commands\MakeCreateShieldCommand::class,
            Commands\MakeInstallShieldCommand::class,
            Commands\MakeGenerateShieldCommand::class,
            Commands\MakeSuperAdminShieldCommand::class,
        ];
    }

    protected function getResources(): array
    {
        return [
            Utils::isResourceEnabled() ? Utils::getResourceClass() : '',
        ];
    }
}
