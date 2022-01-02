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
            ->hasCommands($this->getCommands());
    }

    protected function getCommands(): array
    {
        return [
            Commands\MakeInstallShieldCommand::class,
            Commands\MakeNewShieldCommand::class,
            Commands\MakeGenerateShieldCommand::class,
            Commands\MakePublishShieldCommand::class,
        ];
    }
}
