<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class MakeInstallShieldCommand extends Command
{
    use Concerns\CanManipulateFiles;

    public $signature = 'shield:install
        {--F|fresh}
    ';

    public $description = "One Command to Rule them All 🔥";

    public function handle(): int
    {
        $this->alert('Following operations will be performed:');
        $this->info('-  Publishes core package config');
        $this->info('-  Publishes core package migration');
        $this->warn('   - On fresh applications database will be migrated');
        $this->warn('   - You can also force this behavior by supplying the --fresh option');
        $this->info('-  Creates a filament user');
        $this->warn('   - Assigns Super Admin role if enabled in config');
        $this->warn('   - And/Or Assigns Filament User role if enabled in config');
        $this->info('-  Discovers filament resources and generates Permissions and Policies accordingly');
        $this->warn('   - Will override any existing policies if available');
        $this->info('- Publishes Shield Resource & Page');

        $confirmed = $this->confirm('Do you wish to continue?', true);

        if ($this->CheckIfAlreadyInstalled() && ! $this->option('fresh')) {
            $this->error('Core package(`spatie/laravel-permission`) is already installed!');
            $this->comment('You should run `shield:generate` instead');

            return self::INVALID;
        }

        if ($confirmed) {
            $this->call('vendor:publish', [
                '--provider' => 'Spatie\Permission\PermissionServiceProvider',
            ]);

            $this->info('Core Package config published.');


            if ($this->option('fresh')) {
                $this->call('migrate:fresh');
                $this->info('Database migrations freshed up.');
            } else {
                $this->call('migrate');
                $this->info('Database migrated.');
            }

            $this->call('vendor:publish', [
                '--tag' => 'filament-shield-config',
            ]);

            $this->call('vendor:publish', [
                '--tag' => 'filament-shield-views',
            ]);

            $this->info('Shield config & views published!');

            $this->info('Creating Super Admin...');
            $this->call('shield:super-admin');

            $this->call('shield:publish');

            if (! collect(Filament::getResources())->containsStrict("App\\Filament\\Resources\\Shield\\RoleResource")) {
                Filament::registerResources([
                    \App\Filament\Resources\Shield\RoleResource::class,
                ]);
            }

            if (! collect(Filament::getPages())->containsStrict("App\\Filament\\Pages\\Shield\\Configuration")) {
                Filament::registerResources([
                    \App\Filament\Pages\Shield\Configuration::class,
                ]);
            }

            Artisan::call('shield:generate');

            $this->info('Filament Shield🛡 is now active ✅');
        } else {
            $this->comment('`shield:install` command was cancelled.');
        }

        if ($this->confirm('Would you like to show some love by starring the repo?', true)) {
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

    protected function CheckIfAlreadyInstalled(): bool
    {
        $count = collect(['permissions','roles','role_has_permissions','model_has_roles','model_has_permissions'])
                ->filter(function ($table) {
                    return Schema::hasTable($table);
                })
                ->count();
        if ($count !== 0) {
            return true;
        }

        return false;
    }
}
