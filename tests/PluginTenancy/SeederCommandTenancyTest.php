<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Team;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function createTenancyPermission(array $attributes = []): Permission
{
    return Permission::create(array_merge([
        'name' => fake()->unique()->word() . '_' . fake()->randomNumber(4),
        'guard_name' => 'web',
    ], $attributes));
}

function createTenancyRole(int $teamId, array $attributes = []): Role
{
    return Role::create(array_merge([
        'name' => fake()->unique()->word() . '_' . fake()->randomNumber(4),
        'guard_name' => 'web',
        'team_id' => $teamId,
    ], $attributes));
}

describe('SeederCommand with Tenancy', function () {
    beforeEach(function () {
        $seederPath = database_path('seeders/ShieldSeeder.php');
        if (File::exists($seederPath)) {
            File::delete($seederPath);
        }

        // Enable tenancy
        config()->set('permission.teams', true);

        Role::clearBootedModels();
        Permission::clearBootedModels();

        app()->forgetInstance(PermissionRegistrar::class);
        $registrar = app(PermissionRegistrar::class);
        $registrar->teams = true;
        $registrar->forgetCachedPermissions();
        $registrar->initializeCache();

        $this->team = Team::factory()->create();
        $this->user = User::factory()->create();
        $this->user->teams()->attach($this->team);

        setPermissionsTeamId($this->team->id);
    });

    afterEach(function () {
        $seederPath = database_path('seeders/ShieldSeeder.php');
        if (File::exists($seederPath)) {
            File::delete($seederPath);
        }

        // Reset team context to prevent bleeding into other tests
        setPermissionsTeamId(null);

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Clear cached relations
        $this->user->unsetRelation('roles')->unsetRelation('permissions');

        // Clear booted models to reset any cached state
        Role::clearBootedModels();
        Permission::clearBootedModels();

        // Disable teams mode
        config()->set('permission.teams', false);
    });

    it('automatically exports tenants when tenancy is enabled', function () {
        $permission = createTenancyPermission(['name' => 'view_dashboard']);
        $role = createTenancyRole($this->team->id, ['name' => 'tenant_admin']);
        $role->givePermissionTo($permission);

        $this->artisan('shield:seeder')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->team->name);
        expect($content)->toContain('tenant_admin');
        expect($content)->toContain('view_dashboard');
    });

    it('exports all tenants with --all flag', function () {
        $teamWithRole = $this->team;
        $teamWithoutRole = Team::factory()->create(['name' => 'Team Without Role']);

        $role = createTenancyRole($teamWithRole->id, ['name' => 'admin']);
        createTenancyPermission(['name' => 'test_perm']);

        $this->artisan('shield:seeder', ['--all' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($teamWithRole->name);
        expect($content)->toContain('Team Without Role');
    });

    it('exports only tenants with roles when --all is not used', function () {
        $teamWithRole = $this->team;
        $teamWithoutRole = Team::factory()->create(['name' => 'Team Without Role']);

        $role = createTenancyRole($teamWithRole->id, ['name' => 'admin']);
        createTenancyPermission(['name' => 'test_perm']);

        $this->artisan('shield:seeder')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($teamWithRole->name);
        expect($content)->not->toContain('Team Without Role');
    });

    it('includes tenant_id in roles when tenancy is enabled', function () {
        $permission = createTenancyPermission(['name' => 'view_dashboard']);
        $role = createTenancyRole($this->team->id, ['name' => 'tenant_admin']);
        $role->givePermissionTo($permission);

        $this->artisan('shield:seeder')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        // Check that team_id key exists and has the correct value
        expect($content)->toContain('team_id');
        expect($content)->toContain((string) $this->team->id);
    });

    it('exports tenants, users, and their relationships with --with-users', function () {
        $permission = createTenancyPermission(['name' => 'manage_posts']);
        $role = createTenancyRole($this->team->id, ['name' => 'manager']);
        $role->givePermissionTo($permission);

        $this->user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->user->email);
        expect($content)->toContain('manager');
        expect($content)->toContain('manage_posts');
        expect($content)->toContain($this->team->name);
    });

    it('exports all with --all flag', function () {
        $permission = createTenancyPermission(['name' => 'manage_posts']);
        $role = createTenancyRole($this->team->id, ['name' => 'manager']);
        $role->givePermissionTo($permission);

        $this->user->assignRole($role);

        $this->artisan('shield:seeder', ['--all' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->user->email);
        expect($content)->toContain('manager');
        expect($content)->toContain('manage_posts');
        expect($content)->toContain($this->team->name);
    });

    it('exports user-tenant pivot data when --with-users and tenancy enabled', function () {
        createTenancyPermission(['name' => 'test_perm']);

        $role = createTenancyRole($this->team->id, ['name' => 'owner']);
        $this->user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->user->email);
        expect($content)->toContain($this->team->name);
    });

    it('auto-includes tenants from user-tenant pivot when --with-users is used', function () {
        // Create a second team that has no roles but user is attached to
        $teamWithUserOnly = Team::factory()->create(['name' => 'Team With User Only']);
        $this->user->teams()->attach($teamWithUserOnly->id);

        // Create role in the original team
        $role = createTenancyRole($this->team->id, ['name' => 'admin']);
        createTenancyPermission(['name' => 'test_perm']);

        $this->user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        // Both teams should be included - one from role, one from user pivot
        expect($content)->toContain($this->team->name);
        expect($content)->toContain('Team With User Only');
    });

    it('exports users with tenant-scoped roles', function () {
        $permission = createTenancyPermission(['name' => 'view_reports']);
        $role = createTenancyRole($this->team->id, ['name' => 'reporter']);
        $role->givePermissionTo($permission);

        $this->user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->user->email);
        // In tenancy mode, roles are grouped by tenant
        expect($content)->toContain('tenant_roles');
        expect($content)->toContain('reporter');
    });

    it('exports users from multiple tenants with --all', function () {
        $team2 = Team::factory()->create(['name' => 'Second Team']);
        $user2 = User::factory()->create(['email' => 'user2@test.com']);
        $user2->teams()->attach($team2->id);

        setPermissionsTeamId($this->team->id);
        $role1 = createTenancyRole($this->team->id, ['name' => 'role_team1']);
        $this->user->assignRole($role1);

        setPermissionsTeamId($team2->id);
        $role2 = createTenancyRole($team2->id, ['name' => 'role_team2']);
        $user2->assignRole($role2);

        createTenancyPermission(['name' => 'some_perm']);

        $this->artisan('shield:seeder', ['--all' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->user->email);
        expect($content)->toContain('user2@test.com');
        expect($content)->toContain($this->team->name);
        expect($content)->toContain('Second Team');
    });

    it('exports users with generated random passwords', function () {
        $role = createTenancyRole($this->team->id, ['name' => 'admin']);
        $this->user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--generate-passwords' => 'random',
        ])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->user->email);
        expect($content)->toContain('"password":');
    });

    it('exports users with custom password', function () {
        $role = createTenancyRole($this->team->id, ['name' => 'admin']);
        $this->user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--generate-passwords' => 'SecretPassword123',
        ])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->user->email);
        expect($content)->toContain('"password":');
    });

    it('exports users with included database passwords', function () {
        $role = createTenancyRole($this->team->id, ['name' => 'admin']);
        $this->user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--include-passwords' => true,
        ])
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain($this->user->email);
        expect($content)->toContain('"password":');
    });
});
