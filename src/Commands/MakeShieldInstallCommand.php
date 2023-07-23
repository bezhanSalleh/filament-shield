<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MakeShieldInstallCommand extends Command
{
    public $signature = 'shield:install
        {--F|fresh : re-run the migrations}
        {--O|only : Only setups shield without generating permissions and creating super-admin}
        {--minimal : Output minimal amount of info to console}
    ';

    public $description = 'Setup Core Package requirements and Install Shield';

    public function handle(): int
    {
        if (! Utils::isAuthProviderConfigured()) {
            $this->error('Please make sure your Auth Provider model (App\\Models\\User) uses either `HasRoles` or `HasFilamentShield` trait');

            return self::INVALID;
        }

        if ($this->option('minimal')) {
            $confirmed = true;
        } else {
            $this->alert('Following operations will be performed:');
            $this->info('-  Publishes core package config');
            $this->info('-  Publishes core package migration');
            $this->warn('   - On fresh applications database will be migrated');
            $this->warn('   - You can also force this behavior by supplying the --fresh option');

            $confirmed = $this->confirm('Do you wish to continue?', true);
        }

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

        if (! $this->option('minimal')) {
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
        return collect(['role_has_permissions', 'model_has_roles', 'model_has_permissions', 'roles', 'permissions']);
    }

    protected function install(bool $fresh = false)
    {
        $this->{$this->option('minimal') ? 'callSilent' : 'call'}('vendor:publish', [
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
        ]);

        $this->info('Core Package config published.');

        $this->{$this->option('minimal') ? 'callSilent' : 'call'}('vendor:publish', [
            '--tag' => 'filament-shield-config',
        ]);

        if ($fresh) {
            try {
                Schema::disableForeignKeyConstraints();
                DB::table('migrations')->where('migration', 'like', '%_create_permission_tables')->delete();
                $this->getTables()->each(fn ($table) => DB::statement('DROP TABLE IF EXISTS '.$table));
                Schema::enableForeignKeyConstraints();
            } catch (Throwable $e) {
                $this->info($e);
            }

            $this->info('Freshening up shield migrations.');
        } else {
            $this->info('running shield migrations.');
        }

        $this->{$this->option('minimal') ? 'callSilent' : 'call'}('migrate', [
            '--force' => true,
        ]);

        if (! $this->option('only')) {
            $this->newLine();
            $this->info('Generating permissions ...');
            $this->call('shield:generate', [
                '--all' => true,
                '--minimal' => $this->option('minimal'),
            ]);

            $this->newLine();
            $this->info('Creating a filament user with Super Admin Role...');
            $this->call('shield:super-admin');
        } else {
            $this->call('shield:generate', [
                '--resource' => 'RoleResource',
                '--minimal' => $this->option('minimal'),
            ]);
        }

        $this->info(Artisan::output());

        $this->info('Filament Shield🛡 is now active ✅');
    }
}
