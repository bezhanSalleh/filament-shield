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

    protected function getCommands(): array
    {
        return [
            Commands\MakeUserShieldCommand::class,
            Commands\MakeCreateShieldCommand::class,
            Commands\MakeInstallShieldCommand::class,
            Commands\MakePublishShieldCommand::class,
            Commands\MakeGenerateShieldCommand::class,
        ];
    }
}
