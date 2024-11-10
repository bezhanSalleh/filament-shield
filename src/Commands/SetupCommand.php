<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Stringer;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'shield:setup', description: 'Setup and install core requirements for Shield')]
class SetupCommand extends Command
{
    use Concerns\CanBeProhibitable;
    use Concerns\CanManipulateFiles;

    public $signature = 'shield:setup
        {--F|fresh : re-run the migrations}
        {--minimal : Output minimal amount of info to console}
        {--tenant= : Tenant model}
        {--force}
    ';

    public $description = 'Setup and install core requirements for Shield';

    public function handle(): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        if (! Utils::isAuthProviderConfigured()) {
            $this->components->error('Please make sure your Auth Provider model (\App\\Models\\User) uses either `HasRoles` or `HasFilamentShield` trait');

            return Command::INVALID;
        }

        if ($this->option('minimal')) {
            $confirmed = true;
        } else {
            $this->components->alert('Following operations will be performed:');
            $this->components->info('- Publishes core package config');
            $this->components->info('- Publishes core package migration');
            $this->components->warn('- On fresh applications database will be migrated');
            $this->components->warn('- You can also force this behavior by supplying the --fresh option');

            $confirmed = confirm('Do you wish to continue?');
        }

        if ($this->CheckIfAlreadyInstalled() && ! $this->option('fresh')) {
            $this->components->info('Seems you have already installed the Core package(`spatie/laravel-permission`)!');
            $this->components->info('You should run `shield:install --fresh` instead to refresh the Core package tables and setup shield.');

            if (confirm('Run `shield:setup --fresh` instead?', false)) {
                $this->install(true);
            }

            return Command::INVALID;
        }

        if ($confirmed) {
            $this->install($this->option('fresh'));
        } else {
            $this->components->info('`shield:setup` command was cancelled.');
        }

        if (! $this->option('minimal')) {
            if (confirm('Would you like to show some love by starring the repo?')) {
                if (PHP_OS_FAMILY === 'Darwin') {
                    exec('open https://github.com/bezhanSalleh/filament-shield');
                }
                if (PHP_OS_FAMILY === 'Linux') {
                    exec('xdg-open https://github.com/bezhanSalleh/filament-shield');
                }
                if (PHP_OS_FAMILY === 'Windows') {
                    exec('start https://github.com/bezhanSalleh/filament-shield');
                }

                $this->components->info('Thank you!');
            }
        }

        return Command::SUCCESS;
    }

    protected function CheckIfAlreadyInstalled(): bool
    {
        $count = $this->getTables()
            ->filter(fn (string $table) => Schema::hasTable($table))
            ->count();

        return $count !== 0;
    }

    protected function getTables(): Collection
    {
        return collect(['role_has_permissions', 'model_has_roles', 'model_has_permissions', 'roles', 'permissions']);
    }

    protected function install(bool $fresh = false): void
    {
        $this->{$this->option('minimal') ? 'callSilent' : 'call'}('vendor:publish', [
            '--tag' => 'filament-shield-config',
            '--force' => $this->option('force') || $fresh,
        ]);

        if (filled($tenant = $this->option('tenant'))) {
            $tenantModel = $this->getModel($tenant);

            $shieldConfig = Stringer::for(config_path('filament-shield.php'));

            if (is_null(config()->get('filament-shield.tenant_model', null))) {
                $shieldConfig->prepend('auth_provider_model', "'tenant_model' => null,")
                    ->newLine();
            }

            $shieldConfig
                ->append('tenant_model', "'tenant_model' => '" . get_class($tenantModel) . "',")
                ->deleteLine('tenant_model')
                ->deleteLine("'tenant_model' => null,")
                ->save();

            if (! $this->fileExists(config_path('permission.php')) || $fresh) {
                $this->call('vendor:publish', [
                    '--tag' => 'permission-config',
                    '--force' => true,
                ]);
            }

            Stringer::for(config_path('permission.php'))
                ->replace("'teams' => false,", "'teams' => true,")
                ->save();

            config()->set('permission.teams', true);

            $source = __DIR__ . '/../Support/';
            $destination = app_path('Models');

            $this->copy($source . '/Role.php', $destination . '/Role.php');
            $this->copy($source . '/Permission.php', $destination . '/Permission.php');

            $appServiceProvider = Stringer::for(app_path('Providers/AppServiceProvider.php'));
            if (
                ! $appServiceProvider->containsChainedBlock('app(\Spatie\Permission\PermissionRegistrar::class)
                        ->setPermissionClass(Permission::class)
                        ->setRoleClass(Role::class)')
            ) {
                if (! $appServiceProvider->contains('use App\Models\Role;')) {
                    $appServiceProvider->append('use', 'use App\Models\Role;');
                }

                if (! $appServiceProvider->contains('use App\Models\Permission;')) {
                    $appServiceProvider->append('use', 'use App\Models\Permission;');
                }

                $appServiceProvider
                    ->appendBlock('public function boot()', "
                            app(\Spatie\Permission\PermissionRegistrar::class)
                                ->setPermissionClass(Permission::class)
                                ->setRoleClass(Role::class);
                        ", true)
                    ->save();
            }
        } else {
            $this->call('vendor:publish', [
                '--tag' => 'permission-config',
                '--force' => $this->option('force') || $fresh,
            ]);
        }

        $this->{$this->option('minimal') ? 'callSilent' : 'call'}('vendor:publish', [
            '--tag' => 'permission-migrations',
            '--force' => $this->option('force') || $fresh,
        ]);

        $this->components->info('Core Package config published.');

        if ($fresh) {
            try {
                Schema::disableForeignKeyConstraints();
                DB::table('migrations')->where('migration', 'like', '%_create_permission_tables')->delete();
                $this->getTables()->each(fn ($table) => DB::statement('DROP TABLE IF EXISTS ' . $table));
                Schema::enableForeignKeyConstraints();
            } catch (Throwable $e) {
                $this->components->info($e);
            }

            $this->components->info('Freshening up shield migrations.');
        } else {
            $this->components->info('running shield migrations.');
        }

        $this->{$this->option('minimal') ? 'callSilent' : 'call'}('migrate', [
            '--force' => $fresh,
        ]);

        $this->components->info('Filament Shield🛡 is now active ✅');
    }

    protected function getModel(string $model): ?Model
    {
        $model = str($model)->contains('\\')
            ? $model
            : str($model)->prepend('App\\Models\\')
                ->toString();

        if (! class_exists($model) || ! (app($model) instanceof Model)) {
            $this->components->error("Model not found: {$model}");
            /** @phpstan-ignore-next-line */
            exit();
        }

        return app($model);
    }
}
