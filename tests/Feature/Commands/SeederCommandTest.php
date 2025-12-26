<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\File;
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
});

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

describe('basic functionality', function () {
    it('generates seeder without flags', function () {
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

describe('--with-users flag', function () {
    it('exports users with roles', function () {
        $permission = createPermission(['name' => 'view_users']);
        $role = createRole(['name' => 'admin']);
        $role->givePermissionTo($permission);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create(['email' => 'admin@test.com']);
        $user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
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
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
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

        $this->artisan('shield:seeder', ['--all' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
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
        ])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('role-user@test.com');
        expect($content)->toContain('"roles":["editor"]');
    });

    it('exports users without roles/permissions when using --all', function () {
        $permission = createPermission(['name' => 'test']);

        $userModel = Utils::getAuthProviderFQCN();
        $userWithRole = $userModel::factory()->create(['email' => 'with-role@test.com']);
        $userWithoutAnything = $userModel::factory()->create(['email' => 'no-perms@test.com']);

        $role = createRole(['name' => 'admin']);
        $userWithRole->assignRole($role);

        $this->artisan('shield:seeder', ['--all' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('with-role@test.com');
        expect($content)->toContain('no-perms@test.com');
    });
});

describe('password handling', function () {
    it('errors when both --include-passwords and --generate-passwords are used', function () {
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create();
        $user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--include-passwords' => true,
            '--generate-passwords' => 'random',
        ])->assertFailed();
    });

    it('warns when including hashed passwords', function () {
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

    it('generates random passwords with --generate-passwords=random', function () {
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create(['email' => 'test@test.com']);
        $user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--generate-passwords' => 'random',
        ])
            ->expectsOutputToContain('Generating random passwords for users.')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('"password":');
    });

    it('uses custom password with --generate-passwords=CustomPassword', function () {
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create(['email' => 'test@test.com']);
        $user->assignRole($role);

        $this->artisan('shield:seeder', [
            '--with-users' => true,
            '--generate-passwords' => 'MySecretPassword123',
        ])
            ->expectsOutputToContain('Using provided password for all users.')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('"password":');
    });

    it('prompts for password mode when no password flag provided with --with-users', function () {
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create(['email' => 'test@test.com']);
        $user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('test@test.com');
    });

    it('prompts for password mode when using --all flag', function () {
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create(['email' => 'test@test.com']);
        $user->assignRole($role);

        $this->artisan('shield:seeder', ['--all' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('test@test.com');
    });

    it('includes passwords when user selects include option in prompt', function () {
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('secret'),
        ]);
        $user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'include')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('"password":');
    });

    it('generates passwords when user selects generate option in prompt', function () {
        $role = createRole(['name' => 'admin']);

        $userModel = Utils::getAuthProviderFQCN();
        $user = $userModel::factory()->create(['email' => 'test@test.com']);
        $user->assignRole($role);

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'generate')
            ->expectsQuestion('How would you like to generate passwords?', 'random')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('"password":');
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

        $this->artisan('shield:seeder', ['--with-users' => true])
            ->expectsQuestion('How would you like to handle user passwords?', 'none')
            ->assertSuccessful();

        $content = File::get(database_path('seeders/ShieldSeeder.php'));
        expect($content)->toContain('$user->syncRoles($roles)');
        expect($content)->toContain('$user->syncPermissions($permissions)');
    });
});
