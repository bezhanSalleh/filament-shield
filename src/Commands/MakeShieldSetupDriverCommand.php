<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;

class MakeShieldSetupDriverCommand extends Command
{
    use Concerns\CanRunShellCommands;

    protected $signature = 'shield:setup
        {driver? : The driver to use defaults to the one in the config file }
        {--refresh : Refresh the driver }
        {--uninstall : Uninstall the driver }
    ';

    protected $description = 'Install and setup Shield\'s driver';

    protected $hidden = true;

    public function handle()
    {
        [$refresh, $uninstall] = $this->getComputedOptions();

        if ($refresh) {
            $this->refresh();
        }

        if ($uninstall) {
            $this->uninstall();
        }

        if (! $refresh && ! $uninstall) {
            $this->install();
        }

        return self::SUCCESS;
    }

    protected function getDriver(): string
    {
        return $this->argument('driver') ?? Utils::getDriver();
    }

    protected function install(): void
    {
        $this->requireComposerPackages([
            $this->determineThePackageToInstall(),
        ]);
    }

    protected function uninstall(): void
    {
        $this->clean();

        $this->removeComposerPackages([
            $this->determineThePackageToInstall(),
        ]);
    }

    protected function refresh(): void
    {
        $this->clean();

        if (Utils::isShieldUsingSpatieDriver()) {
            $this->runProcess([
                'php',
                'artisan',
                'vendor:publish',
                '--provider=Spatie\Permission\PermissionServiceProvider',
            ]);
        }

        if (Utils::isShieldUsingBouncerDriver()) {
            $this->runProcess([
                'php',
                'artisan',
                'vendor:publish',
                '--tag=bouncer.migrations',
            ]);
        }

        $this->runProcess([
            'php',
            'artisan',
            'migrate',
        ]);
    }

    protected function getComputedOptions(): array
    {
        $refresh = $this->option('refresh');
        $uninstall = $this->option('uninstall');

        if (! $refresh && ! $uninstall) {
            $refresh = $uninstall = false;
        }

        return [$refresh, $uninstall];
    }

    protected function determineThePackageToInstall(): string
    {
        return match ($this->getDriver()) {
            'spatie' => 'spatie/laravel-permission',
            'bouncer' => 'silber/bouncer',
            default => 'spatie/laravel-permission'
        };
    }

    protected function clean(): void
    {
        Utils::dropTables();
        Utils::removeMigrationFile();

        if (file_exists(config_path('permission.php'))) {
            unlink(config_path('permission.php'));
        }
    }
}
