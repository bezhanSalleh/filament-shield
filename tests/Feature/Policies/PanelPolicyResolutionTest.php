<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShieldServiceProvider;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;

afterEach(function () {
    Filament::setCurrentPanel(null);
});

it('resolves policies to the current panel when enabled', function () {
    eval('namespace App\\Policies\\System; class UserPolicy {}');
    eval('namespace App\\Models; class User extends \\BezhanSalleh\\FilamentShield\\Tests\\Fixtures\\Models\\User {}');

    config()->set('filament-shield.policies.panel_path', true);
    config()->set('filament-shield.policies.panel_aware_resolution', true);

    $panel = Panel::make()->id('system');
    Filament::setCurrentPanel($panel);

    $provider = app()->getProvider(FilamentShieldServiceProvider::class);
    $provider?->packageBooted();

    $policy = Gate::getPolicyFor(\App\Models\User::class);

    expect($policy)->toBeInstanceOf(\App\Policies\System\UserPolicy::class);
});
