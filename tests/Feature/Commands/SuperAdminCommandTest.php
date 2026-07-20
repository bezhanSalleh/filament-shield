<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Commands\SuperAdminCommand;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Team;
use Filament\Panel;
use Filament\PanelRegistry;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $panel = Panel::make()
        ->id('admin')
        ->default()
        ->path('admin')
        ->login()
        ->plugins([FilamentShieldPlugin::make()->centralApp()]);

    app(PanelRegistry::class)->register($panel);

    Route::name('filament.admin.auth.')
        ->prefix($panel->getPath())
        ->group(fn () => Route::get($panel->getLoginRouteSlug(), $panel->getLoginRouteAction())->name('login'));
});

afterEach(function () {
    SuperAdminCommand::createSuperAdminUsing(null);
});

function prepareTeamColumns(): void
{
    $teamFk = config('permission.column_names.team_foreign_key');

    Schema::table('roles', function (Blueprint $table) use ($teamFk) {
        if (! Schema::hasColumn('roles', $teamFk)) {
            $table->unsignedBigInteger($teamFk)->nullable()->after('id');
        }
    });
    Schema::table('model_has_roles', function (Blueprint $table) use ($teamFk) {
        if (! Schema::hasColumn('model_has_roles', $teamFk)) {
            $table->unsignedBigInteger($teamFk)->default(1);
        }
    });
    Schema::table('model_has_permissions', function (Blueprint $table) use ($teamFk) {
        if (! Schema::hasColumn('model_has_permissions', $teamFk)) {
            $table->unsignedBigInteger($teamFk)->default(1);
        }
    });
}

function superAdminUserModel(): string
{
    return Utils::getAuthProviderFQCN();
}

describe('assigning super admin to existing users', function () {
    it('assigns super admin role to user specified via --user option', function () {
        $user = superAdminUserModel()::factory()->create();
        expect($user->hasRole(Utils::getSuperAdminName()))->toBeFalse();

        $this->artisan('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
        ])->assertSuccessful();

        $user->refresh();
        expect($user->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });

    it('auto-selects the only existing user', function () {
        $user = superAdminUserModel()::factory()->create();

        $this->artisan('shield:super-admin', [
            '--panel' => 'admin',
        ])->assertSuccessful();

        $user->refresh();
        expect($user->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });

    it('prompts for user selection when multiple users exist', function () {
        $user1 = superAdminUserModel()::factory()->create();
        $user2 = superAdminUserModel()::factory()->create();

        $this->artisan('shield:super-admin', [
            '--panel' => 'admin',
        ])
            ->expectsQuestion('Please provide the `UserID` to be set as `super_admin`', (string) $user2->id)
            ->assertSuccessful();

        $user2->refresh();
        expect($user2->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });
});

describe('creating super admin interactively', function () {
    it('creates a new user interactively when no users exist', function () {
        $this->artisan('shield:super-admin', [
            '--panel' => 'admin',
        ])
            ->expectsQuestion('Name', 'Test Admin')
            ->expectsQuestion('Email address', 'admin@example.com')
            ->expectsQuestion('Password', 'password123')
            ->assertSuccessful();

        $user = superAdminUserModel()::where('email', 'admin@example.com')->first();
        expect($user)->not->toBeNull()
            ->and($user->name)->toBe('Test Admin')
            ->and($user->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });
});

describe('custom super admin creation via createSuperAdminUsing', function () {
    it('uses custom closure when registered', function () {
        SuperAdminCommand::createSuperAdminUsing(fn () => superAdminUserModel()::create([
            'name' => 'Custom Admin',
            'email' => 'custom@example.com',
            'password' => Hash::make('secret'),
        ]));

        $this->artisan('shield:super-admin', [
            '--panel' => 'admin',
        ])->assertSuccessful();

        $user = superAdminUserModel()::where('email', 'custom@example.com')->first();
        expect($user)->not->toBeNull()
            ->and($user->name)->toBe('Custom Admin')
            ->and($user->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });

    it('falls back to interactive prompts when the closure returns null', function () {
        SuperAdminCommand::createSuperAdminUsing(fn () => null);

        $this->artisan('shield:super-admin', [
            '--panel' => 'admin',
        ])
            ->expectsQuestion('Name', 'Fallback Admin')
            ->expectsQuestion('Email address', 'fallback@example.com')
            ->expectsQuestion('Password', 'password123')
            ->assertSuccessful();

        $user = superAdminUserModel()::where('email', 'fallback@example.com')->first();
        expect($user)->not->toBeNull()
            ->and($user->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });
});

describe('command behavior', function () {
    it('syncs all permissions to the super admin role', function () {
        Permission::create(['name' => 'view_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete_users', 'guard_name' => 'web']);

        $user = superAdminUserModel()::factory()->create();

        $this->artisan('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
        ])->assertSuccessful();

        $role = $user->roles()->where('name', Utils::getSuperAdminName())->first();
        expect($role)->not->toBeNull()
            ->and($role->permissions->pluck('name')->sort()->values()->toArray())
            ->toBe(['delete_users', 'edit_users', 'view_users']);
    });

    it('displays success message with user email', function () {
        $user = superAdminUserModel()::factory()->create(['email' => 'success@example.com']);

        $this->artisan('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
        ])
            ->expectsOutputToContain('success@example.com')
            ->assertSuccessful();
    });

    it('fails when tenancy is enabled without --tenant option', function () {
        config()->set('permission.teams', true);
        app()[PermissionRegistrar::class]->teams = true;

        $user = superAdminUserModel()::factory()->create();

        $this->artisan('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
        ])->assertFailed();
    });

    it('fails before creating a user when tenancy is enabled without --tenant', function () {
        config()->set('permission.teams', true);
        app()[PermissionRegistrar::class]->teams = true;

        $this->artisan('shield:super-admin', ['--panel' => 'admin'])
            ->assertFailed();

        expect(superAdminUserModel()::count())->toBe(0);
    });

    it('fails when the provided tenant does not exist', function () {
        prepareTeamColumns();

        config()->set('permission.teams', true);
        config()->set('filament-shield.tenant_model', Team::class);
        app()[PermissionRegistrar::class]->teams = true;

        $user = superAdminUserModel()::factory()->create();

        $this->artisan('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
            '--tenant' => 999,
        ])->assertFailed();

        $user->refresh()->unsetRelation('roles');
        expect($user->roles)->toBeEmpty();
    });

    it('skips tenant existence validation when no tenant model is configured', function () {
        prepareTeamColumns();

        config()->set('permission.teams', true);
        config()->set('filament-shield.tenant_model', null);
        $registrar = app()[PermissionRegistrar::class];
        $registrar->teams = true;
        $registrar->forgetCachedPermissions();

        $user = superAdminUserModel()::factory()->create();

        $this->artisan('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
            '--tenant' => 999,
        ])->assertSuccessful();

        setPermissionsTeamId(999);
        $user->refresh()->unsetRelation('roles');
        expect($user->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });

    it('assigns super admin role with tenant when --tenant is provided', function () {
        prepareTeamColumns();

        config()->set('permission.teams', true);
        config()->set('filament-shield.tenant_model', Team::class);
        $registrar = app()[PermissionRegistrar::class];
        $registrar->teams = true;
        $registrar->forgetCachedPermissions();

        $team = Team::factory()->create();
        $user = superAdminUserModel()::factory()->create();

        $this->artisan('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin',
            '--tenant' => $team->id,
        ])->assertSuccessful();

        $user->refresh()->unsetRelation('roles');
        expect($user->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });

    it('fails when command is prohibited', function () {
        SuperAdminCommand::prohibit();

        try {
            $this->artisan('shield:super-admin', [
                '--panel' => 'admin',
            ])->assertFailed();
        } finally {
            SuperAdminCommand::prohibit(false);
        }
    });

    it('prompts for panel selection when --panel is not provided', function () {
        $user = superAdminUserModel()::factory()->create();

        $this->artisan('shield:super-admin', [
            '--user' => $user->id,
        ])
            ->expectsQuestion('Which Panel would you like to use?', 'admin')
            ->assertSuccessful();

        $user->refresh();
        expect($user->hasRole(Utils::getSuperAdminName()))->toBeTrue();
    });
});
