<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource;

it('can check if package testing is configured', function () {
    expect(true)->toBeTrue();
});

it('can check if the permission name can be configured using the closure', function () {
    $resource = RoleResource::class;

    // FilamentShield::configurePermissionIdentifierUsing(fn () => str($resource)->afterLast('Resources\\')->replace('\\', '')->headline()->snake()->replace('_', '.'));
    // expect(FilamentShield::getPermissionIdentifier($resource))->toBe('role.resource');
    // FilamentShield::configurePermissionIdentifierUsing(fn () => str($resource)->afterLast('Resources\\')->replace('\\', '')->headline()->snake()->replace('_', '::'));
    // expect(FilamentShield::getPermissionIdentifier($resource))->toBe('role::resource');

    FilamentShield::configurePermissionIdentifierUsing(
        fn($resource) => str($resource::getModel())
            ->afterLast('\\')
            ->lower()
            ->toString()
    );

    expect(FilamentShield::getPermissionIdentifier($resource))->toBe('role');
});
