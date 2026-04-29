<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Commands\GenerateCommand;
use BezhanSalleh\FilamentShield\Commands\InstallCommand;
use BezhanSalleh\FilamentShield\Commands\PublishCommand;
use BezhanSalleh\FilamentShield\Commands\SeederCommand;
use BezhanSalleh\FilamentShield\Commands\SetupCommand;
use BezhanSalleh\FilamentShield\Commands\SuperAdminCommand;
use BezhanSalleh\FilamentShield\Commands\TranslationCommand;
use BezhanSalleh\FilamentShield\Concerns\HasAboutCommand;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use RuntimeException;
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

        if (Utils::isRolePolicyRegistered() && ! Utils::getConfig()->policiesPanelAwareResolutionEnabled()) {
            Gate::policy(Utils::getRoleModel(), Utils::getRolePolicyPath());
        }

        if (Utils::getConfig()->policiesPanelAwareResolutionEnabled()) {
            Gate::guessPolicyNamesUsing(function (string $modelClass): string {
                $appNamespace = app()->getNamespace();

                try {
                    $policySegment = Utils::getPolicyNamespaceSegment();
                } catch (RuntimeException) {
                    $policySegment = 'Policies';
                }

                $policyRoot = rtrim($appNamespace, '\\') . '\\' . $policySegment;

                if ($modelClass === Utils::getRoleModel()) {
                    return $policyRoot . '\\RolePolicy';
                }

                if (Str::startsWith($modelClass, $appNamespace . 'Models\\')) {
                    $relative = Str::of($modelClass)->after($appNamespace . 'Models\\')->toString();

                    return $policyRoot . '\\' . $relative . 'Policy';
                }

                if (Str::startsWith($modelClass, $appNamespace)) {
                    $relative = Str::of($modelClass)->after($appNamespace)->toString();

                    return $policyRoot . '\\' . $relative . 'Policy';
                }

                return Str::of($modelClass)
                    ->replaceFirst('\\Models\\', '\\' . $policySegment . '\\')
                    ->replaceFirst('Models\\', $policySegment . '\\')
                    ->append('Policy')
                    ->toString();
            });
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
            TranslationCommand::class,
        ];
    }
}
