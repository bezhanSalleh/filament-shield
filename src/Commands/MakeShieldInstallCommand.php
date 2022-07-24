<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MakeShieldInstallCommand extends Command
{
    public $signature = 'shield:install
        {--F|fresh}
        {--setting : Setup & Configure shield\'s setting page using setting table.}
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

        $confirmed = $this->confirm('Do you wish to continue?', true);

        if ($this->CheckIfAlreadyInstalled() && ! $this->option('fresh')) {
            $this->comment('Seems you have already installed the Core package(`spatie/laravel-permission`)!');
            $this->comment('You should run `shield:install --fresh` instead to refresh the Core package tables and setup shield.');

            if ($this->confirm('Run `shield:install --fresh` instead?', false)) {
                $this->install(true, $this->option('setting'));
            }

            return self::INVALID;
        }

        if ($confirmed) {
            $this->install($this->option('fresh'), $this->option('setting'));
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
        $count = $this->getTables()
                ->filter(function ($table) {
                    return Schema::hasTable($table);
                })
                ->count();
        if ($count !== 0) {
            return true;
        }

        return false;
    }

    protected function getTables(): Collection
    {
        return collect(['permissions','roles','role_has_permissions','model_has_roles','model_has_permissions','filament_shield_settings']);
    }

    protected function install(bool $fresh = false, bool $setting = false)
    {
        $this->call('vendor:publish', [
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
        ]);

        $this->info('Core Package config published.');

        $this->call('vendor:publish', [
            '--tag' => 'filament-shield-config',
        ]);

        if ($setting) {
            $this->call('vendor:publish', [
                '--tag' => 'filament-shield-seeder',
                '--force' => true,
            ]);

            $this->call('vendor:publish', [
                '--tag' => 'filament-shield-migrations',
                '--force' => true,
            ]);

            $this->info('Shield\'s migration and seeder published.');
        }

        if ($fresh) {
            try {
                Schema::disableForeignKeyConstraints();
                DB::table('migrations')->where('migration', 'like', '%_create_permission_tables')->delete();
                DB::table('migrations')->where('migration', 'like', '%_filament_shield_settings_%')->delete();
                $this->getTables()->each(fn ($table) => DB::statement('DROP TABLE IF EXISTS '.$table));
                Schema::enableForeignKeyConstraints();
            } catch (\Throwable $e) {
                $this->info($e);
            }

            $this->info('Freshening up shield migrations.');
        } else {
            $this->info('running shield migrations.');
        }

        if (! $setting && File::exists($path = glob(database_path('migrations/*_filament_shield_settings_table.php'))[0])) {
            File::delete($path);
        }

        $this->call('migrate');


        if ($setting) {
            Artisan::call('db:seed', [
                '--class' => 'ShieldSettingSeeder',
            ]);

            config()->set('filament-shield', Setting::pluck('value', 'key')->toArray());

            $this->info('Shield\'s settings configured.');
        }

        $this->info('Creating Super Admin...');
        $this->call('shield:super-admin');


        $this->call('shield:generate');
        $this->info(Artisan::output());

        $this->info('Filament Shield🛡 is now active ✅');
    }
}
