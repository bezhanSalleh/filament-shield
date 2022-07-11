<?php

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BezhanSalleh\FilamentShield\FilamentShield;

class FilamentShieldServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-shield')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasCommands($this->getCommands());
    }

    public function packageBooted(): void
    {
        if (config('filament-shield.register_role_policy')) {
            Gate::policy('Spatie\Permission\Models\Role', 'App\Policies\RolePolicy');
        }
    }

    public function packageRegistered(): void
    {
        $this->app->scoped('filament-shield', function (): FilamentShield {
            return new FilamentShield();
        });
    }

    protected function getCommands(): array
    {
        return [
            Commands\MakeCreateShieldCommand::class,
            Commands\MakeInstallShieldCommand::class,
            Commands\MakePublishShieldCommand::class,
            Commands\MakeUpgradeShieldCommand::class,
            Commands\MakeGenerateShieldCommand::class,
            Commands\MakeSuperAdminShieldCommand::class,
        ];
    }
}
