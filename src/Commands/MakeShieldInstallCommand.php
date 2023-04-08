<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Support\Utils;
use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;

class MakeShieldInstallCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanGetBasePath;

    protected $signature = 'shield:install
        {driver? : The driver to install `default:Spatie` }
        {--R|refresh : refresh the driver}
        {--G|generate : Generate everything based on the config, create a super-admin and give all the permissions required}
    ';

    protected $description = 'Installs Shield and setups the driver';

    public function handle(): int
    {
        $this->components->info('Installing Shield...');

        // Publish the config file
        $this->call('vendor:publish', [
            '--tag' => 'filament-shield-config',
        ]);

        // ask for the driver
        $driver = $this->argument('driver') ?? $this->components->choice(
            'Select a driver for Shield?',
            [
                'spatie' => 'Spatie\'s Laravel Permission',
                'bouncer' => 'Joseph Silber\'s Bouncer',
                'custom' => 'Bring Your Own',
            ],
            'spatie'
        );

        $otherPackage = $driver === 'spatie' ? 'silber/bouncer' : 'spatie/laravel-permission';
        $otherDriver = $driver === 'spatie' ? 'bouncer' : 'spatie';
        $installedDriver = $driver === 'spatie' ? 'spatie/laravel-permission' : 'silber/bouncer';

        $this->cleanup($driver);

        if ($driver === 'custom') {
            $this->components->info("Installing the {$driver} driver...");

            $this->swapDriver($driver);

            $this->publishDriverHandler($driver);

            $spatie = 'spatie/laravel-permission';
            $bouncer = 'silber/bouncer';

            if (InstalledVersions::isInstalled($spatie)) {
                $this->components->info('Uninstalling the Spatie driver...');

                $this->call('shield:setup', [
                    'driver' => 'spatie',
                    '--uninstall' => true,
                ]);

                $this->newLine();
                $this->success('Spatie driver uninstalled!');
            }

            if (InstalledVersions::isInstalled($bouncer)) {
                $this->components->info('Uninstalling the `Bouncer` driver...');

                $this->call('shield:setup', [
                    'driver' => 'bouncer',
                    '--uninstall' => true,
                ]);

                $this->newLine();
                $this->success('Bouncer driver uninstalled!');
            }

            $this->success('`Custom` driver installed!');

            if ($this->components->confirm('Would you like to show some love by starring the repo?', true)) {
                Utils::showSomeLove(/* 💖 */);
                $this->line('Thank you!');
            }

            return self::SUCCESS;
        } else {
            if (InstalledVersions::isInstalled($installedDriver) && InstalledVersions::isInstalled($otherPackage)) {
                $this->components->info("Uninstalling the {$otherDriver} driver...");

                $this->call('shield:setup', [
                    'driver' => $otherDriver,
                    '--uninstall' => true,
                ]);

                $this->deleteDriverHandler($otherDriver);

                $this->newLine();
                $this->success("{$otherDriver} driver uninstalled!");
            } elseif (InstalledVersions::isInstalled($otherPackage)) {
                if ($this->components->confirm("The {$otherDriver} driver is already installed. Do you want to remove it and install {$driver} instead?", true)) {
                    $this->components->info("Uninstalling the {$otherDriver} driver...");

                    $this->call('shield:setup', [
                        'driver' => $otherDriver,
                        '--uninstall' => true,
                    ]);

                    $this->deleteDriverHandler($otherDriver);

                    $this->success("{$otherDriver} driver uninstalled!");

                    $this->swapDriver($driver);

                    $this->components->info("Installing the {$driver} driver...");

                    $this->call('shield:setup', [
                        'driver' => $driver,
                    ]);

                    $this->publishDriverHandler($driver);

                    $this->success("{$driver} driver installed!");
                }
            } elseif (InstalledVersions::isInstalled($installedDriver)) {
                if ($this->option('refresh') || $this->components->confirm("The {$driver} driver is already installed. Do you want to refresh it?", true)) {
                    $this->components->info("Refreshing the {$driver} driver...");

                    $this->call('shield:setup', [
                        'driver' => $driver,
                        '--refresh' => true,
                    ]);

                    $this->publishDriverHandler($driver);

                    $this->configureShieldUserProvider();

                    $this->success("{$driver} driver refreshed!");

                    return self::SUCCESS;
                }
            } else {
                $this->components->info("Installing the {$driver} driver...");
                $this->swapDriver($driver);

                $this->call('shield:setup', [
                    'driver' => $driver,
                ]);

                $this->publishDriverHandler($driver);

                $this->success("{$driver} driver installed!");
            }
        }

        $this->call('shield:setup', [
            'driver' => $driver,
            '--refresh' => true,
        ]);

        $this->manageDriverConfig();
        $this->configureShieldUserProvider();

        if ($this->option('generate')) {
            $this->call('shield:generate');
            $this->call('shield:super-admin');
        }

        if ($this->components->confirm('Would you like to show some love by starring the repo?', true)) {
            Utils::showSomeLove(/* 💖 */);
            $this->line('Thank you!');
        }

        return self::SUCCESS;
    }

    protected function swapDriver(string $driver): void
    {
        $current = config('filament-shield.driver');

        config(['filament-shield.driver' => $driver]);

        if ($current !== $driver) {
            $this->replaceInFile(
                $current,
                $driver,
                config_path('filament-shield.php')
            );
        }
    }

    protected function success(string $message)
    {
        $this->line('  <bg=bright-green;fg=white;> SUCCESS </> '.$message);
        $this->newLine();
    }

    protected function getDriverHandler(string $driver): string
    {
        return match ($driver) {
            'custom' => 'CustomShieldDriver.php',
            'spatie' => 'SpatieShieldDriver.php',
            'bouncer' => 'BouncerShieldDriver.php',
        };
    }

    protected function publishDriverHandler(string $driver): void
    {
        if (! $this->checkForCollision([$this->getBasePath().DIRECTORY_SEPARATOR.$this->getDriverHandler($driver)])) {
            $this->copyStubToApp($driver, $this->getBasePath().DIRECTORY_SEPARATOR.$this->getDriverHandler($driver));
        }
    }

    protected function deleteDriverHandler(string $driver): void
    {
        $filesystem = new Filesystem();

        if ($this->fileExists($this->getBasePath().DIRECTORY_SEPARATOR.$this->getDriverHandler($driver))) {
            $filesystem->delete($this->getBasePath().DIRECTORY_SEPARATOR.$this->getDriverHandler($driver));
        }
    }

    protected function cleanup(string $driver): void
    {
        $path = $this->getShieldUserProviderPath();

        $filesystem = new Filesystem();

        $currentHandler = $this->getDriverHandler($driver);

        $risdualHandlers = [
            'CustomShieldDriver.php',
            'SpatieShieldDriver.php',
            'BouncerShieldDriver.php',
        ];

        unset($risdualHandlers[$currentHandler]);

        foreach ($risdualHandlers as $handler) {
            if ($this->fileExists($this->getBasePath().DIRECTORY_SEPARATOR.$handler)) {
                $filesystem->delete($this->getBasePath().DIRECTORY_SEPARATOR.$handler);
            }
        }

        if ($this->existsInFile('use Silber\Bouncer\Database\HasRolesAndAbilities;', $path)) {
            $this->replaceInFile(
                'use Silber\Bouncer\Database\HasRolesAndAbilities;',
                '',
                $path
            );
            $this->replaceInFile(
                'use HasRolesAndAbilities;',
                '',
                $path
            );
        }

        if ($this->existsInFile('use Spatie\Permission\Traits\HasRoles;', $path)) {
            $this->replaceInFile(
                'use Spatie\Permission\Traits\HasRoles;',
                '',
                $path
            );
            $this->replaceInFile(
                'use HasRoles;',
                '',
                $path
            );
        }
    }

    protected function manageDriverConfig(): void
    {
        $spatie = "'name' => 'super_admin',
        'define_via_gate' => false,
        'intercept_gate' => 'before', // after
        ";

        if (Utils::isShieldUsingSpatieDriver()) {
            if (! $this->existsInFile("'define_via_gate' => false,", config_path('filament-shield.php'))) {
                $this->replaceInFile(
                    "'name' => 'super_admin',",
                    $spatie,
                    config_path('filament-shield.php')
                );
            }
        } else {
            $this->replaceInFile(
                "'define_via_gate' => false,",
                '',
                config_path('filament-shield.php')
            );
            $this->replaceInFile(
                "'intercept_gate' => 'before', // after",
                '',
                config_path('filament-shield.php')
            );
        }
    }

    protected function getShieldUserProviderPath(): string
    {
        $model = Utils::getAuthProviderFQCN();

        return (new ReflectionClass(new $model()))->getFileName();
    }

    protected function configureShieldUserProvider(): void
    {
        $path = $this->getShieldUserProviderPath();

        if (Utils::isShieldUsingSpatieDriver()) {
            if ($this->existsInFile('use Silber\Bouncer\Database\HasRolesAndAbilities;', $path)) {
                $this->replaceInFile(
                    'use Silber\Bouncer\Database\HasRolesAndAbilities;',
                    'use Spatie\Permission\Traits\HasRoles;',
                    $path
                );
                $this->replaceInFile(
                    'use HasRolesAndAbilities;',
                    'use HasRoles;',
                    $path
                );
            }

            if (! $this->existsInFile('use Spatie\Permission\Traits\HasRoles;', $path)) {
                $this->replaceInFile(
                    'use Illuminate\Foundation\Auth\User as Authenticatable;',
                    'use Illuminate\Foundation\Auth\User as Authenticatable;'.PHP_EOL.'use Spatie\Permission\Traits\HasRoles;',
                    $path
                );

                $this->replaceInFile(
                    '{',
                    '{'.PHP_EOL.'    use HasRoles;',
                    $path
                );
            }
        }

        if (Utils::isShieldUsingBouncerDriver()) {
            if ($this->existsInFile('use Spatie\Permission\Traits\HasRoles;', $path)) {
                $this->replaceInFile(
                    'use Spatie\Permission\Traits\HasRoles;',
                    'use Silber\Bouncer\Database\HasRolesAndAbilities;',
                    $path
                );
                $this->replaceInFile(
                    'use HasRoles;',
                    'use HasRolesAndAbilities;',
                    $path
                );
            }

            if (! $this->existsInFile('use Silber\Bouncer\Database\HasRolesAndAbilities;', $path)) {
                $this->replaceInFile(
                    'use Illuminate\Foundation\Auth\User as Authenticatable;',
                    'use Illuminate\Foundation\Auth\User as Authenticatable;'.PHP_EOL.'use Silber\Bouncer\Database\HasRolesAndAbilities;',
                    $path
                );

                $this->replaceInFile(
                    '{',
                    '{'.PHP_EOL.'    use HasRolesAndAbilities;',
                    $path
                );
            }
        }
    }
}
