<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;

beforeEach(function () {
    $this->app->bind('filament', function () {
        return new class
        {
            public function getResources()
            {
                return [];
            }
        };
    });
});

// ──────────────────────────────────────────────────────────
// Separator / case conflict validation
// ──────────────────────────────────────────────────────────

it('throws exception when underscore separator is used with snake case', function () {
    config([
        'filament-shield.permissions.separator' => '_',
        'filament-shield.permissions.case' => 'snake',
    ]);

    $shield = new FilamentShield;
    $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
})->throws(InvalidArgumentException::class);

it('throws exception when underscore separator is used with lower_snake case', function () {
    config([
        'filament-shield.permissions.separator' => '_',
        'filament-shield.permissions.case' => 'lower_snake',
    ]);

    $shield = new FilamentShield;
    $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
})->throws(InvalidArgumentException::class);

it('throws exception when underscore separator is used with upper_snake case', function () {
    config([
        'filament-shield.permissions.separator' => '_',
        'filament-shield.permissions.case' => 'upper_snake',
    ]);

    $shield = new FilamentShield;
    $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
})->throws(InvalidArgumentException::class);

it('throws exception when hyphen separator is used with kebab case', function () {
    config([
        'filament-shield.permissions.separator' => '-',
        'filament-shield.permissions.case' => 'kebab',
    ]);

    $shield = new FilamentShield;
    $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
})->throws(InvalidArgumentException::class);

it('allows underscore separator with pascal case', function () {
    config([
        'filament-shield.permissions.separator' => '_',
        'filament-shield.permissions.case' => 'pascal',
    ]);

    $shield = new FilamentShield;
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);

    expect($permissions)->toHaveKey('view');
    expect($permissions['view']['key'])->toBeString();
});

it('allows underscore separator with camel case', function () {
    config([
        'filament-shield.permissions.separator' => '_',
        'filament-shield.permissions.case' => 'camel',
    ]);

    $shield = new FilamentShield;
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);

    expect($permissions)->toHaveKey('view');
    expect($permissions['view']['key'])->toBeString();
});

it('allows dot separator with any case', function () {
    $cases = ['snake', 'kebab', 'pascal', 'camel', 'upper_snake', 'lower_snake'];

    foreach ($cases as $case) {
        config([
            'filament-shield.permissions.separator' => '.',
            'filament-shield.permissions.case' => $case,
        ]);

        $shield = new FilamentShield;
        $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);

        expect($permissions)->toHaveKey('view');
    }
});

it('allows colon separator with any case', function () {
    $cases = ['snake', 'kebab', 'pascal', 'camel', 'upper_snake', 'lower_snake'];

    foreach ($cases as $case) {
        config([
            'filament-shield.permissions.separator' => ':',
            'filament-shield.permissions.case' => $case,
        ]);

        $shield = new FilamentShield;
        $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);

        expect($permissions)->toHaveKey('view');
    }
});
