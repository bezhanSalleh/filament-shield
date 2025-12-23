<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Team;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $seederPath = database_path('seeders/ShieldSeeder.php');
    if (File::exists($seederPath)) {
        File::delete($seederPath);
    }
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
});

afterEach(function () {
    $seederPath = database_path('seeders/ShieldSeeder.php');
    if (File::exists($seederPath)) {
        File::delete($seederPath);
    }
    config()->set('permission.teams', false);
});

function enableTenancy(): void
{
    config()->set('permission.teams', true);
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
}

function createPermission(array $attributes = []): Permission
{
    return Permission::create(array_merge([
        'name' => fake()->unique()->word() . '_' . fake()->randomNumber(4),
        'guard_name' => 'web',
    ], $attributes));
}

function createRole(array $attributes = []): Role
{
    return Role::create(array_merge([
        'name' => fake()->unique()->word() . '_' . fake()->randomNumber(4),
        'guard_name' => 'web',
    ], $attributes));
}

function createTenantRole(int $teamId, array $attributes = []): Role
{
    return Role::create(array_merge([
        'name' => fake()->unique()->word() . '_' . fake()->randomNumber(4),
        'guard_name' => 'web',
        'team_id' => $teamId,
    ], $attributes));
}

function createTenantWithRole(string $roleName = 'admin', array $permissions = []): array
{
    $team = Team::factory()->create(['name' => 'Test Team']);
    setPermissionsTeamId($team->id);

    $role = createTenantRole($team->id, ['name' => $roleName]);

    foreach ($permissions as $permName) {
        $permission = createPermission(['name' => $permName]);
        $role->givePermissionTo($permission);
    }

    return compact('team', 'role');
}

describe('backward compatibility', function () {
    it('generates seeder without new flags', function () {
        $permission = createPermission(['name' => 'view_users']);
        $role = createRole(['name' => 'admin']);
        $role->givePermissionTo($permission);

        $this->artisan('shield:seeder')
            ->assertSuccessful();

        $seederPath = database_path('seeders/ShieldSeeder.php');
        expect(File::exists($seederPath))->toBeTrue();

        $content = File::get($seederPath);
        expect($content)->toContain('admin');
        expect($content)->toContain('view_users');
    });

    it('respects --option=permissions_via_roles flag', function () {
        $permission1 = createPermission(['name' => 'view_users']);
        $permission2 = createPermission(['name' => 'standalone_permission']);
        $role = createRole(['name' => 'admin']);
        $role->givePermissionTo($permission1);

        $this->artisan('shield:seeder', ['--option' => 'permissions_via_roles'])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('admin');
        expect($content)->toContain('view_users');
    });

    it('respects --option=direct_permissions flag', function () {
        $permission1 = createPermission(['name' => 'view_users']);
        $permission2 = createPermission(['name' => 'standalone_permission']);
        $role = createRole(['name' => 'admin']);
        $role->givePermissionTo($permission1);

        $this->artisan('shield:seeder', ['--option' => 'direct_permissions'])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('standalone_permission');
    });

    it('warns when no roles or permissions exist', function () {
        $this->artisan('shield:seeder')
            ->expectsOutputToContain('No roles or permissions found to export.')
            ->assertFailed();
    });

    it('prevents overwriting existing seeder without --force', function () {
        createPermission(['name' => 'test']);

        $this->artisan('shield:seeder')->assertSuccessful();

        $this->artisan('shield:seeder')
            ->assertFailed();
    });

    it('overwrites existing seeder with --force flag', function () {
        createPermission(['name' => 'test']);

        $this->artisan('shield:seeder')->assertSuccessful();

        $this->artisan('shield:seeder', ['--force' => true])
            ->assertSuccessful();
    });
});

describe('--with-tenants flag', function () {
    it('exits with failure when tenancy is not enabled', function () {
        createPermission(['name' => 'test']);

        config()->set('filament-shield.tenant_model', null);

        $this->artisan('shield:seeder', ['--with-tenants' => true])
            ->expectsOutputToContain('Tenancy is not enabled.')
            ->assertFailed();
    });

    it('exports tenants when tenancy is enabled', function () {
        if (! Schema::hasColumn('roles', 'team_id')) {
            $this->markTestSkipped('Teams migrations required for this test');
        }

        enableTenancy();

        $data = createTenantWithRole('tenant_admin', ['view_dashboard']);

        $this->artisan('shield:seeder', ['--with-tenants' => true])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('Test Team');
    });

    it('exports all tenants with --all flag', function () {
        if (! Schema::hasColumn('roles', 'team_id')) {
            $this->markTestSkipped('Teams migrations required for this test');
        }

        enableTenancy();

        $teamWithRole = Team::factory()->create(['name' => 'Team With Role']);
        $teamWithoutRole = Team::factory()->create(['name' => 'Team Without Role']);

        setPermissionsTeamId($teamWithRole->id);
        $role = createTenantRole($teamWithRole->id, ['name' => 'admin']);
        createPermission(['name' => 'test_perm']);

        $this->artisan('shield:seeder', ['--with-tenants' => true, '--all' => true])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('Team With Role');
        expect($content)->toContain('Team Without Role');
    });

    it('exports only tenants with roles when --all is not used', function () {
        if (! Schema::hasColumn('roles', 'team_id')) {
            $this->markTestSkipped('Teams migrations required for this test');
        }

        enableTenancy();

        $teamWithRole = Team::factory()->create(['name' => 'Team With Role']);
        $teamWithoutRole = Team::factory()->create(['name' => 'Team Without Role']);

        setPermissionsTeamId($teamWithRole->id);
        $role = createTenantRole($teamWithRole->id, ['name' => 'admin']);
        createPermission(['name' => 'test_perm']);

        $this->artisan('shield:seeder', ['--with-tenants' => true])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('Team With Role');
        expect($content)->not->toContain('Team Without Role');
    });
});

describe('--with-users flag', function () {
    it('exports users with roles', function () {
        $permission = createPermission(['name' => 'view_users']);
        $role = createRole(['name' => 'admin']);
        $role->givePermissionTo($permission);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create(['email' => 'admin@test.com']);
        $user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('admin@test.com');
        expect($content)->toContain('"roles":["admin"]');
    });

    it('exports users with direct permissions', function () {
        $permission = createPermission(['name' => 'special_access']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create(['email' => 'special@test.com']);
        $user->givePermissionTo($permission);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('special@test.com');
        expect($content)->toContain('"permissions":["special_access"]');
    });

    it('exports all users with --all flag regardless of roles', function () {
        createPermission(['name' => 'test']);

        $userModel = Utils::getAuthProviderFQCN();
        $userWithRole = $userModel::factory()->create(['email' => 'with-role@test.com']);
        $userWithoutRole = $userModel::factory()->create(['email' => 'without-role@test.com']);

        $role = createRole(['name' => 'admin']);
        $userWithRole->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true, '--all' => true])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('with-role@test.com');
        expect($content)->toContain('without-role@test.com');
    });

    it('respects --option flag when exporting users', function () {
        $permission = createPermission(['name' => 'direct_perm']);
        $role = createRole(['name' => 'editor']);

        $userModel = Utils::getAuthProviderFQCN();
        $userWithRole = $userModel::factory()->create(['email' => 'role-user@test.com']);
        $userWithPermission = $userModel::factory()->create(['email' => 'perm-user@test.com']);

        $userWithRole->assignRole($role);
        $userWithPermission->givePermissionTo($permission);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--option' => 'permissions_via_roles',
        ])->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('role-user@test.com');
        expect($content)->toContain('"roles":["editor"]');
    });
});

describe('password flags', function () {
    it('errors when both password flags are used', function () {
        createPermission(['name' => 'test']);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--include-passwords' => true,
            '--generate-passwords' => true,
        ])->assertFailed();
    });

    it('warns when including hashed passwords', function () {
        $permission = createPermission(['name' => 'test']);
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('secret123'),
        ]);
        $user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--include-passwords' => true,
        ])
            ->expectsOutputToContain('Including hashed passwords. Handle generated seeder securely.')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('"password":');
    });
});

describe('combined flags', function () {
    it('exports tenants, users, and their relationships', function () {
        if (! Schema::hasColumn('roles', 'team_id')) {
            $this->markTestSkipped('Teams migrations required for this test');
        }

        enableTenancy();

        $team = Team::factory()->create(['name' => 'Combined Test Team']);
        setPermissionsTeamId($team->id);

        $permission = createPermission(['name' => 'manage_posts']);
        $role = createTenantRole($team->id, ['name' => 'manager']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create(['email' => 'manager@test.com']);
        $user->teams()->attach($team->id);
        $user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-tenants' => true,
            '--with-users' => true,
            '--all' => true,
        ])->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('manager@test.com');
        expect($content)->toContain('manager');
        expect($content)->toContain('manage_posts');
        expect($content)->toContain('Combined Test Team');
    });

    it('exports user-tenant pivot data when both flags are used', function () {
        if (! Schema::hasColumn('roles', 'team_id')) {
            $this->markTestSkipped('Teams migrations required for this test');
        }

        enableTenancy();

        $team = Team::factory()->create(['name' => 'Pivot Test Team']);
        setPermissionsTeamId($team->id);

        createPermission(['name' => 'test_perm']);

        $user = User::factory()->create(['email' => 'pivot@test.com']);
        $user->teams()->attach($team->id, ['is_owner' => true]);

        $role = createTenantRole($team->id, ['name' => 'owner']);
        $user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-tenants' => true,
            '--with-users' => true,
        ])->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('pivot@test.com');
        expect($content)->toContain('Pivot Test Team');
    });
});

describe('seeder stub content', function () {
    it('generates valid PHP syntax', function () {
        $permission = createPermission(['name' => 'test_permission']);
        $role = createRole(['name' => 'test_role']);
        $role->givePermissionTo($permission);

        $this->artisan('shield:seeder')->assertSuccessful();

        $seederPath = database_path('seeders/ShieldSeeder.php');
        $content = File::get($seederPath);

        expect($content)->toContain('namespace Database\Seeders;');
        expect($content)->toContain('class ShieldSeeder extends Seeder');
        expect($content)->toContain('public function run(): void');
        expect($content)->toContain('forgetCachedPermissions()');
    });

    it('includes syncRoles and syncPermissions methods for users', function () {
        $permission = createPermission(['name' => 'test']);
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create();
        $user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('$user->syncRoles($roles)');
        expect($content)->toContain('$user->syncPermissions($permissions)');
    });
});
