<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests;

use BezhanSalleh\FilamentShield\FilamentShieldServiceProvider;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Team;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    protected bool $withTenancy = false;

    protected function getPackageProviders($app)
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            PermissionServiceProvider::class,
            FilamentShieldServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // Database
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Spatie Permission
        config()->set('permission.models.permission', Permission::class);
        config()->set('permission.models.role', Role::class);
        config()->set('permission.cache.key', 'spatie.permission.cache');
        config()->set('permission.teams', $this->withTenancy);
        config()->set('permission.column_names.team_foreign_key', 'team_id');

        // Shield
        config()->set('filament-shield.auth_provider_model', User::class);
        config()->set('filament-shield.tenant_model', Team::class);

        // Auth
        config()->set('auth.defaults.guard', 'web');
        config()->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);
        config()->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);

        // Application
        config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        config()->set('view.compiled', sys_get_temp_dir());
    }

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'BezhanSalleh\\FilamentShield\\Tests\\database\\factories\\' . class_basename($modelName) . 'Factory'
        );

        $this->setupFreshDatabase();

        view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    }

    /**
     * Set up a fresh database for each test.
     *
     * Critical for proper isolation between TestCase and TenancyTestCase
     * which require different database schemas (with/without team_id columns).
     */
    protected function setupFreshDatabase(): void
    {
        config()->set('permission.teams', $this->withTenancy);

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();

        $testMigrations = [
            __DIR__ . '/database/migrations/0001_01_01_000000_create_users_table.php',
            __DIR__ . '/database/migrations/0001_01_01_000001_create_teams_table.php',
            __DIR__ . '/database/migrations/0001_01_01_000002_create_team_user_table.php',
        ];

        foreach ($testMigrations as $migrationPath) {
            $migration = include $migrationPath;
            $migration->up();
        }

        $basePath = __DIR__ . '/../vendor/spatie/laravel-permission/database/migrations/';

        $baseMigration = include $basePath . 'create_permission_tables.php.stub';
        $baseMigration->up();

        if ($this->withTenancy) {
            $teamsMigration = include $basePath . 'add_teams_fields.php.stub';
            $teamsMigration->up();
        }

        $registrar = app(PermissionRegistrar::class);
        $registrar->teams = $this->withTenancy;
        $registrar->forgetCachedPermissions();
    }
}
