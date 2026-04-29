<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Facades\Filament;
use Filament\Panel;

beforeEach(function () {
    $panel = Panel::make()->id('admin');
    Filament::setCurrentPanel($panel);
});

afterEach(function () {
    Filament::setCurrentPanel(null);
});

it('does not prefix permission keys when panel prefix is disabled', function () {
    config()->set('filament-shield.permissions.panel_prefix', false);

    $shield = new FilamentShield;
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
    $key = $permissions['view']['key'];

    expect($key)->not->toStartWith('admin:');
});

it('prefixes resource permission keys with panel id when enabled', function () {
    config()->set('filament-shield.permissions.panel_prefix', true);

    $shield = new FilamentShield;
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
    $key = $permissions['view']['key'];

    expect($key)->toStartWith('admin:');
});

it('falls back to permissions separator when panel separator is missing', function () {
    config()->set('filament-shield.permissions.panel_prefix', true);
    config()->set('filament-shield.permissions.panel_prefix_separator', null);
    config()->set('filament-shield.permissions.separator', '_');

    $shield = new FilamentShield;
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
    $key = $permissions['view']['key'];

    expect($key)->toStartWith('admin_');
});

it('prefixes custom permission keys with panel id when enabled', function () {
    config()->set('filament-shield.permissions.panel_prefix', true);
    config()->set('filament-shield.custom_permissions', [
        'manage_reports' => 'Manage Reports',
    ]);

    $shield = new FilamentShield;
    $permissions = $shield->getCustomPermissions();

    expect(array_keys($permissions))->each->toStartWith('admin:');
});
