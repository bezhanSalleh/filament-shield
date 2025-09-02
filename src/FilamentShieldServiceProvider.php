<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Commands\GenerateCommand;
use BezhanSalleh\FilamentShield\Commands\InstallCommand;
use BezhanSalleh\FilamentShield\Commands\PublishCommand;
use BezhanSalleh\FilamentShield\Commands\SeederCommand;
use BezhanSalleh\FilamentShield\Commands\SetupCommand;
use BezhanSalleh\FilamentShield\Commands\SuperAdminCommand;
use BezhanSalleh\FilamentShield\Concerns\HasAboutCommand;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentShieldServiceProvider extends PackageServiceProvider
{
    use HasAboutCommand;

    public static string $name = 'filament-shield';

    public static string $viewNamespace = 'filament-shield';

    /** {@inheritDoc} */
    public function configurePackage(Package $package): void
    {
        /**
         * @var Package $package
         */
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasCommands($this->getCommands());
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-shield', fn (): FilamentShield => new FilamentShield);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        $this->initAboutCommand();

        if (Utils::isSuperAdminDefinedViaGate()) {
            Gate::{Utils::getSuperAdminGateInterceptionStatus()}(fn (object $user, string $ability): ?bool => match (Utils::getSuperAdminGateInterceptionStatus()) {
                'before' => $user->hasRole(Utils::getSuperAdminName()) ? true : null,
                'after' => $user->hasRole(Utils::getSuperAdminName()),
                default => false
            });
        }

        if (Utils::isRolePolicyRegistered()) {
            Gate::policy(Utils::getRoleModel(), Utils::getRolePolicyPath());
        }
    }

    /**
     * @return array<class-string>
     */
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
