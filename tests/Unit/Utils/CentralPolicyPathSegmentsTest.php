<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Support\Utils;

it('defaults central policy path segments to vendor and src', function (): void {
    config()->set('filament-shield.policies.central_path_segments', null);

    expect(Utils::getCentralPolicyPathSegments())->toBe(['vendor', 'src']);
});

it('reads central policy path segments from config', function (): void {
    config()->set('filament-shield.policies.central_path_segments', ['vendor', 'packages']);

    expect(Utils::getCentralPolicyPathSegments())->toBe(['vendor', 'packages']);
});

it('uses central policy path when model path matches a segment', function (): void {
    config()->set('filament-shield.policies.central_path_segments', ['vendor', 'src']);

    expect(Utils::shouldUseCentralPolicyPath('/app/vendor/acme/models/User.php'))->toBeTrue()
        ->and(Utils::shouldUseCentralPolicyPath('/app/modules/Users/src/Models/Admin.php'))->toBeTrue();
});

it('uses co-located policy path when no segment matches', function (): void {
    config()->set('filament-shield.policies.central_path_segments', ['vendor']);

    expect(Utils::shouldUseCentralPolicyPath('/app/modules/Users/src/Models/Admin.php'))->toBeFalse()
        ->and(Utils::shouldUseCentralPolicyPath('/app/app/Models/Post.php'))->toBeFalse();
});

it('never uses central policy path when segments are empty', function (): void {
    config()->set('filament-shield.policies.central_path_segments', []);

    expect(Utils::shouldUseCentralPolicyPath('/app/vendor/acme/models/User.php'))->toBeFalse();
});
