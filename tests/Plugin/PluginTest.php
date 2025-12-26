<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;

beforeEach(function () {
    // Mock Filament to avoid NoDefaultPanelSetException for tests that don't need it
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

describe('plugin instantiation', function () {
    it('can be instantiated', function () {
        $plugin = FilamentShieldPlugin::make();

        expect($plugin)->toBeInstanceOf(FilamentShieldPlugin::class);
    });

    it('can be configured as central app', function () {
        $plugin = FilamentShieldPlugin::make()->centralApp();

        expect($plugin->isCentralApp())->toBeTrue();
    });

    it('default is not central app', function () {
        $plugin = FilamentShieldPlugin::make();

        expect($plugin->isCentralApp())->toBeFalse();
    });

    it('has correct plugin id', function () {
        $plugin = FilamentShieldPlugin::make();

        expect($plugin->getId())->toBe('filament-shield');
    });
});

describe('plugin configuration', function () {
    it('returns correct auth provider model', function () {
        expect(Utils::getAuthProviderFQCN())->toBe(User::class);
    });

    it('detects tenancy is disabled when no tenant panel is set', function () {
        expect(Utils::isTenancyEnabled())->toBeFalse();
    });

    it('has configurable super admin role', function () {
        config()->set('filament-shield.super_admin.name', 'super-admin');

        expect(Utils::getSuperAdminName())->toBe('super-admin');
    });

    it('has configurable panel user role', function () {
        config()->set('filament-shield.panel_user.name', 'panel-user');

        expect(Utils::getPanelUserRoleName())->toBe('panel-user');
    });

    it('has super admin enabled by default', function () {
        expect(Utils::isSuperAdminEnabled())->toBeTrue();
    });

    it('has panel user role enabled by default', function () {
        expect(Utils::isPanelUserRoleEnabled())->toBeTrue();
    });
});

describe('RoleResource configuration', function () {
    it('has correct model', function () {
        expect(RoleResource::getModel())->toBe(\Spatie\Permission\Models\Role::class);
    });

    it('has record title attribute', function () {
        $reflection = new ReflectionClass(RoleResource::class);
        $property = $reflection->getProperty('recordTitleAttribute');

        expect($property->getDefaultValue())->toBe('name');
    });

    it('can get slug from config', function () {
        expect(Utils::getResourceSlug())->toBeString();
    });

    it('can get cluster from config', function () {
        // Cluster can be null or a class name
        $cluster = Utils::getResourceCluster();
        expect($cluster === null || is_string($cluster))->toBeTrue();
    });
});
