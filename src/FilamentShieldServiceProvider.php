<?php

namespace BezhanSalleh\FilamentShield;

use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Contracts\ShieldDriver;
use BezhanSalleh\FilamentShield\Resources\RoleResource;

class FilamentShieldServiceProvider extends PluginServiceProvider
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

        if (Utils::isShieldUsingSpatieDriver()) {
            if (Utils::isSuperAdminDefinedViaGate()) {
                Gate::{Utils::getSuperAdminGateInterceptionStatus()}(function ($user, $ability) {
                    return match (Utils::getSuperAdminGateInterceptionStatus()) {
                        'before' => $user->hasRole(Utils::getSuperAdminName()) ? true : null,
                        'after' => $user->hasRole(Utils::getSuperAdminName()),
                        default => false
                    };
                });
            }

            if (Utils::isRolePolicyRegistered()) {
                Gate::policy(Utils::getRoleModel(), 'App\Policies\RolePolicy');
            }
        }
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton('shield', function () {
            $driverManager = new ShieldManager();

            return $driverManager::make();
        });

        $this->app->alias('shield', ShieldDriver::class);
        
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
            Commands\MakeShieldSetupDriverCommand::class,
        ];
    }

    protected function getResources(): array
    {
        if (Utils::isResourcePublished()) {
            return [];
        }

        return [
            RoleResource::class,
        ];
    }
}
