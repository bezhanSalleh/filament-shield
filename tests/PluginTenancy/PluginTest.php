<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Team;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;

beforeEach(function () {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create();
    $this->user->teams()->attach($this->team);
});

describe('tenancy configuration', function () {
    it('returns the correct tenant model from config', function () {
        expect(Utils::getTenantModel())->toBe(Team::class);
    });

    it('has tenant model foreign key configured', function () {
        expect(Utils::getTenantModelForeignKey())->toBe('team_id');
    });
});

describe('plugin tenancy mode', function () {
    it('plugin can be configured without central app mode', function () {
        $plugin = FilamentShieldPlugin::make();

        expect($plugin->isCentralApp())->toBeFalse();
    });

    it('central app mode can be toggled', function () {
        $plugin = FilamentShieldPlugin::make()->centralApp(true);
        expect($plugin->isCentralApp())->toBeTrue();

        $plugin->centralApp(false);
        expect($plugin->isCentralApp())->toBeFalse();
    });
});

describe('SyncShieldTenant middleware', function () {
    it('middleware class exists', function () {
        expect(class_exists(SyncShieldTenant::class))->toBeTrue();
    });

    it('middleware can be instantiated', function () {
        $middleware = new SyncShieldTenant;
        expect($middleware)->toBeInstanceOf(SyncShieldTenant::class);
    });
});

describe('acting as tenant user', function () {
    it('sets authentication context', function () {
        $this->actingAs($this->user);

        expect(auth()->check())->toBeTrue();
        expect(auth()->id())->toBe($this->user->id);
    });

    it('sets permission team context', function () {
        setPermissionsTeamId($this->team->id);

        expect(getPermissionsTeamId())->toBe($this->team->id);
    });
});
