<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use BezhanSalleh\FilamentShield\Stringer;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'shield:setup', description: 'Setup and install core requirements for Shield')]
class SetupCommand extends Command
{
    use CanManipulateFiles;
    use Prohibitable;

    /** @var string */
    public $signature = 'shield:setup
        {--F|fresh : re-run the migrations}
        {--tenant= : Tenant model}
        {--force}
        {--starred : Skip the prompt to star the repo}
    ';

    protected string $callingMethod = 'call';

    protected bool $refresh = false;

    protected bool $shouldConfigureTenancy = false;

    protected ?string $tenantModel = null;

    public function handle(): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        $this->refresh = $this->option('fresh') ?? false;
        $this->callingMethod = $this->option('verbose') ? 'call' : 'callSilently';
        $this->tenantModel = $this->option('tenant');
        $this->shouldConfigureTenancy = filled($this->tenantModel);

        if (! Utils::isAuthProviderConfigured()) {
            $this->components->error('Please make sure your Auth Provider model (\App\\Models\\User) uses the `HasRoles` trait');
        }

        if ($this->isShieldInstalled() && ! $this->refresh) {
            $confirmed = confirm('Shield is already installed. Would you like to reinstall?', false);

            $this->refresh = $confirmed;

            if (! $confirmed) {
                return Command::INVALID;
            }
        }

        $this->publishConfigs();

        $this->configureTenancy();

        $this->manageMigrations();

        if (! $this->option('verbose')) {
            $this->components->info('Shield has been successfully setup & configured!');
        }

        if (confirm('Would you like to run `shield:install`?', true)) {
            $this->promptForOtherShieldCommands();
        }

        $this->components->info('Filament Shield🛡 is now active ✅');

        if (! $this->option('starred') && confirm('Would you like to show some love by starring the repo?', true)) {
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

        return Command::SUCCESS;
    }

    protected function promptForOtherShieldCommands(): void
    {
        $installOptions = [];

        $panel = select(
            label: 'Which Panel would you like to install Shield for?',
            options: collect(Filament::getPanels())->keys(),
            required: true
        );

        $installOptions['panel'] = $panel;

        $makePanelTenantable = $this->shouldConfigureTenancy && confirm("Would you like to make the `{$panel}` panel tenantable?", false);

        Process::forever()->tty()->run("php artisan shield:install {$panel} " . ($makePanelTenantable ? '--tenant' : ''));

        if (confirm("Would you like to run `shield:generate` for `{$panel}` Panel?", true)) {
            Process::forever()->tty()->run("php artisan shield:generate --all --panel={$panel}");
        }
        if (confirm("Would you like to run `shield:super-admin` for `{$panel}` Panel?", true)) {
            $this->newLine();
            Process::forever()->tty()->run("php artisan shield:super-admin --panel={$panel}");
        }
    }

    protected function isShieldInstalled(): bool
    {
        $count = $this->getTables()
            ->filter(fn (string $table) => Schema::hasTable($table))
            ->count();

        return $count !== 0;
    }

    protected function getTables(): Collection
    {
        return collect(config('permission.table_names', ['role_has_permissions', 'model_has_roles', 'model_has_permissions', 'roles', 'permissions']));
    }

    protected function configureTenancy(): void
    {
        if (! $this->shouldConfigureTenancy && ($this->refresh || ! $this->isShieldInstalled()) && confirm('Do you want to configure Shield for multi-tenancy?', false)) {
            $this->tenantModel = text(label: 'Please provide the Team/Tenant model (e.g App\\Models\\Team)', required: true);
            $this->shouldConfigureTenancy = true;
        }

        if ($this->shouldConfigureTenancy) {
            $tenantModel = $this->getModel($this->tenantModel);

            $shieldConfig = Stringer::for(config_path('filament-shield.php'));

            if (is_null(config()->get('filament-shield.tenant_model', null))) {
                $shieldConfig->prepend('auth_provider_model', "'tenant_model' => null,")
                    ->newLine();
            }

            $shieldConfig
                ->append('tenant_model', "'tenant_model' => '" . $tenantModel::class . "',")
                ->deleteLine('tenant_model')
                ->deleteLine("'tenant_model' => null,")
                ->save();

            Stringer::for(config_path('permission.php'))
                ->replace("'teams' => false,", "'teams' => true,")
                ->save();

            config()->set('permission.teams', true);

            $source = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR;
            $destination = app_path('Models');

            $this->copy($source . 'Role.php', $destination . DIRECTORY_SEPARATOR . 'Role.php');
            $this->copy($source . 'Permission.php', $destination . DIRECTORY_SEPARATOR . 'Permission.php');

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
        }
    }

    protected function publishConfigs(): void
    {
        $force = $this->refresh || $this->option('force');

        if (! $this->fileExists(config_path('filament-shield.php')) || $force) {
            $this->{$this->callingMethod}('vendor:publish', [
                '--tag' => 'filament-shield-config',
                '--force' => $force,
            ]);
        }

        if (! $this->fileExists(config_path('permission.php')) || $force) {
            $this->{$this->callingMethod}('vendor:publish', [
                '--tag' => 'permission-config',
                '--force' => $force,
            ]);
        }
    }

    protected function manageMigrations(): void
    {
        $forced = $this->refresh || $this->option('force');
        if ($forced) {
            Schema::disableForeignKeyConstraints();
            DB::table('migrations')->where('migration', 'like', '%create_permission_tables')->delete();
            $this->getTables()->each(fn (string $table) => DB::statement('DROP TABLE IF EXISTS ' . $table));
            Schema::enableForeignKeyConstraints();
        }

        $this->{$this->callingMethod}('vendor:publish', [
            '--tag' => 'permission-migrations',
            '--force' => $forced,
        ]);

        if ($forced) {
            Process::quietly()
                ->run('php artisan migrate --force');
        } else {
            $this->{$this->callingMethod}('migrate');
        }
    }

    protected function getModel(string $model): ?Model
    {
        $model = str($model)->contains('\\')
            ? $model
            : str($model)->prepend('App\\Models\\')
                ->toString();

        if (! class_exists($model) || ! (app($model) instanceof Model)) {
            $this->components->error("Model not found: {$model}");
            exit();
        }

        return app($model);
    }
}
