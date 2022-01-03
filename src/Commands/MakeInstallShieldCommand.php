<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class MakeInstallShieldCommand extends Command
{
    public $signature = 'shield:install {--fresh}';

    public $description = "Installs everything and generates Permissions & Policies for existing Filament Resources";

    public function handle(): int
    {
        $confirmed = $this->confirm('Do you wish to continue?', true);

        https://github.com/bezhanSalleh/filament-shield

        if ($this->CheckIfAlreadyInstalled() && !$this->option('fresh')) {
            $this->error('Core package(`spatie/laravel-permission`) is already installed!');
            $this->comment('You should run `shield:generate` instead');
            return self::INVALID;
        }

        if ($confirmed) {
            // publish core package migration and config
            $this->callSilently('vendor:publish',[
                '--provider' => 'Spatie\Permission\PermissionServiceProvider'
            ]);

            //run core pacakge migrations
            if ($this->option('fresh')) {
                $this->call('migrate:fresh');
            } else {
                $this->call('migrate');
            }

            $this->info('Creating user...');
            $this->call('shield:user');
            $this->call('shield:generate');
            $this->call('shield:publish');

            $this->info('Filament Shield🛡 is now active ✅');
        } else {
            $this->comment('`shield:install` command was cancelled.');
        }

        if (\Spatie\Permission\Models\Role::count() === 0 && $this->confirm('Would you like to show some love by starring the repo?', true)) {
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/bezhanSalleh/filament-shield');
            }
            if (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/bezhanSalleh/filament-shield');
            }
            if (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/bezhanSalleh/filament-shield');
            }

            $this->line('Thank you!');
        }
        return self::SUCCESS;
    }

    protected function CheckIfAlreadyInstalled():bool
    {
        $count = collect(['permissions','roles','role_has_permissions','model_has_roles','model_has_permissions'])
                ->filter(function($table) {
                    return Schema::hasTable($table);
                })
                ->count();
        if ($count != 0 || class_exists('Spatie\Permission\Models\Role')) {
            return true;
        }

        return false;
    }
}
