<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeInstallShieldCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanBackupAFile;

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

        $confirmed = $this->confirm('Do you wish to continue?', true);

        if ($this->CheckIfAlreadyInstalled() && ! $this->option('fresh')) {
            $this->comment('Seems you have already installed the Core package(`spatie/laravel-permission`)!');
            $this->comment('You should run `shield:install --fresh` instead to refresh the Core package tables and setup shield.');

            if ($this->confirm('Run `shield:install --fresh` instead?', false)) {
                $this->install(true);
            }

            return self::INVALID;
        }

        if ($confirmed) {
            $this->install($this->option('fresh'));
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
        return collect(['permissions','roles','role_has_permissions','model_has_roles','model_has_permissions']);
    }

    protected function install(bool $fresh = false)
    {
        $this->call('vendor:publish', [
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
        ]);

        $this->info('Core Package config published.');


        if ($fresh) {
            try {
                Schema::disableForeignKeyConstraints();
                DB::table('migrations')->where('migration', 'like', '%_create_permission_tables')->delete();
                $this->getTables()->each(fn ($table) => DB::statement('DROP TABLE IF EXISTS '.$table));
                Schema::enableForeignKeyConstraints();
            } catch (\Throwable $e) {
                $this->info($e);
            }

            $this->call('migrate');
            $this->info('Database migrations freshed up.');

            (new Filesystem())->ensureDirectoryExists(config_path());

            if ($this->isBackupPossible(config_path('filament-shield.php'), config_path('filament-shield.php.bak'))) {
                $this->info('Config backup created.');
            }

            (new Filesystem())->copy(__DIR__.'/../../config/filament-shield.php', config_path('filament-shield.php'));
        } else {
            $this->call('migrate');
            $this->info('Database migrated.');
        }

        (new Filesystem())->ensureDirectoryExists(lang_path());
        (new Filesystem())->copyDirectory(__DIR__.'/../../resources/lang', lang_path('/vendor/filament-shield'));

        (new Filesystem())->ensureDirectoryExists(lang_path());
        (new Filesystem())->copyDirectory(__DIR__.'/../../resources/views', resource_path('/views/vendor/filament-shield'));

        $this->info('Published Shields\' translations, views & Resource.');

        $this->info('Creating Super Admin...');
        $this->call('shield:super-admin');

        if (config('filament-shield.exclude.enabled')) {
            Artisan::call('shield:generate --exclude');
            $this->info(Artisan::output());
        } else {
            Artisan::call('shield:generate');
            $this->info(Artisan::output());
        }

        $this->info('Filament Shield🛡 is now active ✅');
    }
}
