<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Spatie\Permission\Models\Role;

afterEach(function () {
    Filament::setCurrentPanel(null);
});

it('prefixes role names for non-default panels when enabled', function () {
    config()->set('filament-shield.roles.panel_prefix', true);
    config()->set('filament-shield.roles.panel_prefix_separator', ':');

    $panel = Panel::make()->id('system')->plugins([
        FilamentShieldPlugin::make(),
    ]);
    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);

    expect(Utils::prefixRoleName('admin'))->toBe('system:admin');
    expect(Utils::stripPanelRolePrefix('system:admin'))->toBe('admin');
});

it('keeps role names unprefixed for default panel', function () {
    config()->set('filament-shield.roles.panel_prefix', true);
    config()->set('filament-shield.roles.panel_prefix_separator', ':');

    $panel = Panel::make()->id('app')->default();
    Filament::setCurrentPanel($panel);

    expect(Utils::prefixRoleName('admin'))->toBe('admin');
});

it('falls back to permission panel separator when role separator is missing', function () {
    config()->set('filament-shield.roles.panel_prefix', true);
    config()->set('filament-shield.roles.panel_prefix_separator', null);
    config()->set('filament-shield.permissions.panel_prefix_separator', '-');

    $panel = Panel::make()->id('system');
    Filament::setCurrentPanel($panel);

    expect(Utils::prefixRoleName('admin'))->toBe('system-admin');
});

it('keeps default panel roles while excluding other panel prefixes', function () {
    config()->set('filament-shield.roles.panel_prefix', true);
    config()->set('filament-shield.roles.panel_prefix_separator', ':');

    // Only filter known panel prefixes, not every role containing the separator.
    $defaultPanel = Panel::make()->id('app')->default();
    $systemPanel = Panel::make()->id('system');

    app(PanelRegistry::class)->register($defaultPanel);
    app(PanelRegistry::class)->register($systemPanel);
    Filament::setCurrentPanel($systemPanel);
    Filament::setCurrentPanel($defaultPanel);

    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'system:admin', 'guard_name' => 'web']);
    Role::create(['name' => 'team:lead', 'guard_name' => 'web']);

    $names = RoleResource::getEloquentQuery()->pluck('name')->all();

    expect($names)->toContain('admin');
    expect($names)->toContain('team:lead');
    expect($names)->not->toContain('system:admin');
});
