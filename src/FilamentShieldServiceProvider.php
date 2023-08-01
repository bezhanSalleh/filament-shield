<?php

namespace BezhanSalleh\FilamentShield;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentShieldServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-shield')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasCommands($this->getCommands());
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-shield', function (): FilamentShield {
            return new FilamentShield();
        });
    }

    protected function getCommands(): array
    {
        return [
            Commands\MakeShieldDoctorCommand::class,
            Commands\MakeShieldSeederCommand::class,
            Commands\MakeShieldPublishCommand::class,
            Commands\MakeShieldUpgradeCommand::class,
            Commands\MakeShieldInstallCommand::class,
            Commands\MakeShieldGenerateCommand::class,
            Commands\MakeShieldSuperAdminCommand::class,
        ];
    }
}
