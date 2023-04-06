<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use BezhanSalleh\FilamentShield\Support\Utils;

class MakeShieldInstallCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $signature = 'shield:install
        {driver? : The driver to install `default:Spatie` }
        {--R|refresh : refresh the driver}
    ';

    protected $description = 'Installs Shield and setups the driver';


    public function handle(): int
    {
        $this->info(app()->version());
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

        if (str($driver)->contains('Custom')) {

        } else {

            $otherPackage = $driver === 'spatie' ? 'silber/bouncer' : 'spatie/laravel-permission';
            $otherDriver = $driver === 'spatie' ? 'bouncer' : 'spatie';
            $installedDriver = $driver === 'spatie' ? 'spatie/laravel-permission' : 'silber/bouncer';

            if (InstalledVersions::isInstalled($installedDriver) && InstalledVersions::isInstalled($otherPackage)) {

                $this->components->info("Uninstalling the {$otherDriver} driver...");

                $this->call('shield:setup', [
                    'driver' => $otherDriver,
                    '--uninstall' => true,
                ]);

                $this->success("{$otherDriver} driver uninstalled!");
            }

            else if (InstalledVersions::isInstalled($otherPackage)) {
                if ($this->components->confirm("The {$otherDriver} driver is already installed. Do you want to remove it and install {$driver} instead?", true)) {

                    $this->components->info("Uninstalling the {$otherDriver} driver...");

                    $this->call('shield:setup', [
                        'driver' => $otherDriver,
                        '--uninstall' => true,
                    ]);

                    $this->success("{$otherDriver} driver uninstalled!");

                    $this->swapDriver($driver);

                    $this->components->info("Installing the {$driver} driver...");

                    $this->call('shield:setup', [
                        'driver' => $driver,
                    ]);

                    $this->success("{$driver} driver installed!");
                }
            } elseif (InstalledVersions::isInstalled($installedDriver)) {
                if ($this->option('refresh') || $this->components->confirm("The {$driver} driver is already installed. Do you want to refresh it?",true)) {

                    $this->components->info("Refreshing the {$driver} driver...");

                    $this->call('shield:setup', [
                        'driver' => $driver,
                        '--refresh' => true,
                    ]);

                    $this->success("{$driver} driver refreshed!");

                    return self::SUCCESS;
                }
            } else {

                $this->components->info("Installing the {$driver} driver...");

                    $this->call('shield:setup', [
                        'driver' => $driver,
                    ]);

                    $this->success("{$driver} driver installed!");
            }
        }

        $this->call('shield:setup', [
            'driver' => $driver,
            '--refresh' => true,
        ]);

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
        $this->line('  <bg=bright-green;fg=white;> SUCCESS </> ' . $message);
        $this->newLine();
    }
}
