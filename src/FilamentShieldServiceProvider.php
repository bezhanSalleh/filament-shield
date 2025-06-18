<?php

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Commands\SetupCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BezhanSalleh\FilamentShield\Commands\SeederCommand;
use BezhanSalleh\FilamentShield\Commands\InstallCommand;
use BezhanSalleh\FilamentShield\Commands\PublishCommand;
use BezhanSalleh\FilamentShield\Commands\GenerateCommand;
use BezhanSalleh\FilamentShield\Concerns\HasAboutCommand;
use BezhanSalleh\FilamentShield\Commands\SuperAdminCommand;

class FilamentShieldServiceProvider extends PackageServiceProvider
{
    use HasAboutCommand;

    public static string $name = 'filament-shield';

    public static string $viewNamespace = 'filament-shield';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$name)
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

        $this->initAboutCommand();

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
            Gate::policy(Utils::getRoleModel(), 'App\\' . Utils::getPolicyNamespace() . '\\RolePolicy');
        }
    }

    protected function getCommands(): array
    {
        return [
            GenerateCommand::class,
            InstallCommand::class,
            PublishCommand::class,
            SeederCommand::class,
            SetupCommand::class,
            SuperAdminCommand::class,
        ];
    }
}
