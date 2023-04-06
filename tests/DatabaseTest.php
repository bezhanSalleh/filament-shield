<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Shield;
use BezhanSalleh\FilamentShield\ShieldManager;
use BezhanSalleh\FilamentShield\Support\Utils;
use Composer\InstalledVersions;

it('can check if package testing is configured', function () {
    expect(true)->toBeTrue();
});

it('can check if the permission name can be configured using the closure', function () {
    $resource = RoleResource::class;

    // FilamentShield::configurePermissionIdentifierUsing(fn () => str($resource)->afterLast('Resources\\')->replace('\\', '')->headline()->snake()->replace('_', '.'));
    expect(FilamentShield::getPermissionIdentifier($resource))->toBe('role');
});

// it can check if the ShieldDriver hasRole method is called
// it('can check if the ShieldDriver hasRole method is called', function () {
//     $user = new stdClass();
//     $role = 'admin';

//     $shield = Mockery::mock(Shield::class);
//     $shield->shouldReceive('hasRole')->once()->with($user, $role)->andReturn(true);
//     expect($shield->hasRole($user, $role))->toBeTrue();

// });

it('can check if the ShieldDriver hasRole method is called', function () {
    $user = new stdClass();
    $role = 'admin';

    $shield = Mockery::mock(Shield::class);
    $shield->shouldReceive('hasRole')->once()->with($user, $role)->andReturn(true);
    expect(! $shield->hasRole($user, 'admin'))->toBeFalse();
});

it('can check if the ShieldDriver hasPermission method is called', function () {
    $user = new stdClass();
    $permission = 'admin';

    $shield = Mockery::mock(ShieldManager::class);
    $shield->shouldReceive('hasPermission')->once()->with($user, $permission)->andReturn(true);
    expect($shield->hasPermission($user, $permission))->toBeTrue();
});

it('can check if the driver is spatie', function () {
    $driver = str(Utils::getDriver())->ucfirst()->append('Driver')->prepend('App\\Filament\\Resources\\Shield\\')->toString();
    expect($driver)
        ->toBeString("App\Filament\Resources\Shield\SpatieDriver");

    // expect(InstalledVersions::isInstalled('filament/filament'))->toBeTrue();
});

it('can check if the driver is bouncer', function () {
    config()->set('filament-shield.driver.name', 'bouncer');
    $driver = str(Utils::getDriver())->ucfirst()->append('Driver')->prepend('App\\Filament\\Resources\\Shield\\')->toString();
    dd(get_class(new $driver()));
    expect($driver)
        ->toBeString("App\Filament\Resources\Shield\BouncerDriver");

    // expect(InstalledVersions::isInstalled('filament/filament'))->toBeTrue();
});

it('can test if the CanManageComposerDependencies trait is working', function () {
    $this->assertTrue(true);
});
