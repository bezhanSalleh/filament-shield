<?php

namespace BezhanSalleh\FilamentShield;

use Filament\PluginServiceProvider;
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
            ->hasCommands($this->getCommands());
    }

    public function packageBooted(): void
    {
        parent::packageBooted();
        
        if (config('filament-shield.register_role_policy')) {
            \Illuminate\Support\Facades\Gate::policy('Spatie\Permission\Models\Role', 'App\Policies\RolePolicy');
        }
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-shield', function (): \BezhanSalleh\FilamentShield\FilamentShield {
            return new \BezhanSalleh\FilamentShield\FilamentShield();
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
