<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Team;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\actingAs;

describe('RoleResource with Tenancy', function () {
    beforeEach(function () {
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

        actingAs($this->user);
        setPermissionsTeamId($this->team->id);
    });

    afterEach(function () {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->user->unsetRelation('roles')->unsetRelation('permissions');
    });

    describe('tenant scoped role operations', function () {
        it('creates roles scoped to tenant', function () {
            // Manual instantiation required to bypass Role::create() caching issues when running with non-tenancy tests
            $role = new Role;
            $role->name = 'manager';
            $role->guard_name = 'web';
            $role->team_id = $this->team->id;
            $role->save();

            $freshRole = Role::find($role->id);
            expect($freshRole->team_id)->toBe($this->team->id);
        });

        it('allows same role name in different tenants', function () {
            $tenant1 = $this->team;
            $tenant2 = Team::factory()->create();

            setPermissionsTeamId($tenant1->id);
            $role1 = new Role;
            $role1->name = 'admin';
            $role1->guard_name = 'web';
            $role1->team_id = $tenant1->id;
            $role1->save();

            setPermissionsTeamId($tenant2->id);
            $role2 = new Role;
            $role2->name = 'admin';
            $role2->guard_name = 'web';
            $role2->team_id = $tenant2->id;
            $role2->save();

            expect($role1->id)->not->toBe($role2->id);
            expect($role1->name)->toBe($role2->name);
            expect($role1->team_id)->not->toBe($role2->team_id);
            expect(Role::where('name', 'admin')->count())->toBe(2);
        });
    });

    describe('tenant scoped user roles', function () {
        it('assigns tenant-scoped role to user', function () {
            $role = Role::create([
                'name' => 'team_editor',
                'guard_name' => 'web',
                'team_id' => $this->team->id,
            ]);

            $this->user->assignRole($role);

            expect($this->user->hasRole('team_editor'))->toBeTrue();
        });

        it('user can have different roles in different tenants', function () {
            $tenant1 = $this->team;
            $tenant2 = Team::factory()->create();
            $this->user->teams()->attach($tenant2);

            setPermissionsTeamId($tenant1->id);
            $adminRole = Role::create([
                'name' => 'admin',
                'guard_name' => 'web',
                'team_id' => $tenant1->id,
            ]);
            $this->user->assignRole($adminRole);

            setPermissionsTeamId($tenant2->id);
            $viewerRole = Role::create([
                'name' => 'viewer',
                'guard_name' => 'web',
                'team_id' => $tenant2->id,
            ]);
            $this->user->assignRole($viewerRole);

            setPermissionsTeamId($tenant1->id);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $this->user->unsetRelation('roles');
            expect($this->user->hasRole('admin'))->toBeTrue();

            setPermissionsTeamId($tenant2->id);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $this->user->unsetRelation('roles');
            expect($this->user->hasRole('viewer'))->toBeTrue();
        });
    });

    describe('tenant scoped permissions', function () {
        it('creates permissions and assigns to tenant role', function () {
            $permission = Permission::create([
                'name' => 'view_reports',
                'guard_name' => 'web',
            ]);

            $role = Role::create([
                'name' => 'reporter',
                'guard_name' => 'web',
                'team_id' => $this->team->id,
            ]);

            $role->givePermissionTo($permission);

            expect($role->hasPermissionTo('view_reports'))->toBeTrue();
        });

        it('respects tenant context for permission checks', function () {
            $permission = Permission::create([
                'name' => 'do_something',
                'guard_name' => 'web',
            ]);

            $role = Role::create([
                'name' => 'doer',
                'guard_name' => 'web',
                'team_id' => $this->team->id,
            ]);
            $role->givePermissionTo($permission);
            $this->user->assignRole($role);

            expect($this->user->can('do_something'))->toBeTrue();
        });
    });

    describe('user tenant relationships', function () {
        it('attaches user to tenant', function () {
            expect($this->user->teams)->toHaveCount(1);
            expect($this->user->teams->first()->id)->toBe($this->team->id);
        });

        it('user can belong to multiple tenants', function () {
            $tenant2 = Team::factory()->create();
            $this->user->teams()->attach($tenant2);

            expect($this->user->fresh()->teams)->toHaveCount(2);
        });
    });
});
