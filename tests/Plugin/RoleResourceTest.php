<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Resources\Roles\Pages\CreateRole;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\EditRole;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Tests\Fixtures\AdminPanelProvider;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->app->register(AdminPanelProvider::class);

    $this->user = User::factory()->create();
    actingAs($this->user);
});

describe('resource configuration', function () {
    it('has correct model', function () {
        expect(RoleResource::getModel())->toBe(Role::class);
    });

    it('has record title attribute defined', function () {
        $reflection = new ReflectionClass(RoleResource::class);
        $property = $reflection->getProperty('recordTitleAttribute');

        expect($property->getDefaultValue())->toBe('name');
    });

    it('has all required pages', function () {
        $pages = RoleResource::getPages();

        expect($pages)->toHaveKeys(['index', 'create', 'view', 'edit']);
        expect($pages['index']->getPage())->toBe(ListRoles::class);
        expect($pages['create']->getPage())->toBe(CreateRole::class);
        expect($pages['view']->getPage())->toBe(ViewRole::class);
        expect($pages['edit']->getPage())->toBe(EditRole::class);
    });

    it('can get slug', function () {
        $slug = RoleResource::getSlug();

        expect($slug)->toBeString();
        expect($slug)->toBe(Utils::getResourceSlug());
    });

    it('can get cluster', function () {
        $cluster = RoleResource::getCluster();

        expect($cluster)->toBe(Utils::getResourceCluster());
    });
});

describe('role page classes', function () {
    it('ListRoles extends correct parent', function () {
        expect(class_parents(ListRoles::class))
            ->toContain(\Filament\Resources\Pages\ListRecords::class);
    });

    it('CreateRole extends correct parent', function () {
        expect(class_parents(CreateRole::class))
            ->toContain(\Filament\Resources\Pages\CreateRecord::class);
    });

    it('EditRole extends correct parent', function () {
        expect(class_parents(EditRole::class))
            ->toContain(\Filament\Resources\Pages\EditRecord::class);
    });

    it('ViewRole extends correct parent', function () {
        expect(class_parents(ViewRole::class))
            ->toContain(\Filament\Resources\Pages\ViewRecord::class);
    });
});

describe('role CRUD operations', function () {
    it('can create a role', function () {
        $role = Role::create([
            'name' => 'test_role',
            'guard_name' => 'web',
        ]);

        expect($role)->toBeInstanceOf(Role::class);
        expect($role->name)->toBe('test_role');
        expect($role->guard_name)->toBe('web');
    });

    it('can update a role', function () {
        $role = Role::create([
            'name' => 'old_name',
            'guard_name' => 'web',
        ]);

        $role->update(['name' => 'new_name']);

        expect($role->fresh()->name)->toBe('new_name');
    });

    it('can delete a role', function () {
        $role = Role::create([
            'name' => 'deletable_role',
            'guard_name' => 'web',
        ]);
        $roleId = $role->id;

        $role->delete();

        expect(Role::find($roleId))->toBeNull();
    });

    it('can assign permissions to a role', function () {
        $permission1 = Permission::create(['name' => 'view_users', 'guard_name' => 'web']);
        $permission2 = Permission::create(['name' => 'edit_users', 'guard_name' => 'web']);

        $role = Role::create([
            'name' => 'user_manager',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo([$permission1, $permission2]);

        expect($role->permissions)->toHaveCount(2);
        expect($role->hasPermissionTo('view_users'))->toBeTrue();
        expect($role->hasPermissionTo('edit_users'))->toBeTrue();
    });

    it('can sync permissions on a role', function () {
        $permission1 = Permission::create(['name' => 'perm_a', 'guard_name' => 'web']);
        $permission2 = Permission::create(['name' => 'perm_b', 'guard_name' => 'web']);
        $permission3 = Permission::create(['name' => 'perm_c', 'guard_name' => 'web']);

        $role = Role::create([
            'name' => 'synced_role',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo([$permission1, $permission2]);
        expect($role->permissions)->toHaveCount(2);

        $role->syncPermissions([$permission2, $permission3]);
        $role->refresh();

        expect($role->permissions)->toHaveCount(2);
        expect($role->hasPermissionTo('perm_a'))->toBeFalse();
        expect($role->hasPermissionTo('perm_b'))->toBeTrue();
        expect($role->hasPermissionTo('perm_c'))->toBeTrue();
    });

    it('can revoke permission from a role', function () {
        $permission = Permission::create(['name' => 'revokable', 'guard_name' => 'web']);

        $role = Role::create([
            'name' => 'revoke_test_role',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo($permission);
        expect($role->hasPermissionTo('revokable'))->toBeTrue();

        $role->revokePermissionTo($permission);
        expect($role->hasPermissionTo('revokable'))->toBeFalse();
    });
});
