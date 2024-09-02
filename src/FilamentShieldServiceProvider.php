<?php

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Gate;
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
            ->hasViews()
            ->hasCommands($this->getCommands());
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-shield', function (): FilamentShield {
            return new FilamentShield;
        });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

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
