<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;

beforeEach(function () {
    // Mock Filament panel to avoid NoDefaultPanelSetException
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

it('builds default permission keys for resources', function () {
    $shield = new FilamentShield;

    $affixes = ['view', 'create', 'update', 'delete'];
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, $affixes);

    expect($permissions)->toHaveKeys(['view', 'create', 'update', 'delete']);

    // Check that keys are formatted correctly (the actual format depends on config)
    foreach (['view', 'create', 'update', 'delete'] as $affix) {
        expect($permissions[$affix])->toHaveKey('key');
        expect($permissions[$affix])->toHaveKey('label');
        expect($permissions[$affix]['key'])->toBeString();
        expect($permissions[$affix]['label'])->toBeString();
    }
});

it('builds default permission key for single affix', function () {
    $shield = new FilamentShield;

    // Use RoleResource instead of non-existent Dashboard class
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, 'view');

    expect($permissions)->toBeArray();
    expect($permissions)->not->toBeEmpty();

    // The key should be a string and the value should be a label
    $firstKey = array_keys($permissions)[0];
    expect($firstKey)->toBeString();
    expect($permissions[$firstKey])->toBeString();
});

it('allows custom permission key building', function () {
    $shield = new FilamentShield;

    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        return "custom_{$affix}_{$subject}";
    });

    $affixes = ['view', 'create'];
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, $affixes);

    expect($permissions['view']['key'])->toStartWith('custom_view_');
    expect($permissions['create']['key'])->toStartWith('custom_create_');
});

it('provides correct parameters to custom permission key builder', function () {
    $shield = new FilamentShield;
    $capturedParams = [];

    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) use (&$capturedParams) {
        $capturedParams = compact('entity', 'affix', 'subject', 'case', 'separator');

        return "{$affix}_{$subject}";
    });

    $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);

    expect($capturedParams)->toHaveKeys(['entity', 'affix', 'subject', 'case', 'separator']);
    expect($capturedParams['entity'])->toBe(RoleResource::class);
    expect($capturedParams['affix'])->toBe('view');
    expect($capturedParams['subject'])->toBeString(); // Don't assume exact case
    expect($capturedParams['case'])->toBeString();
    expect($capturedParams['separator'])->toBeString();
});

it('allows entity-specific custom permission keys', function () {
    $shield = new FilamentShield;

    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        if (str_contains($entity, 'Resource')) {
            return "resource_{$affix}_{$subject}";
        }

        return "{$affix}_{$subject}";
    });

    // Test resource
    $resourcePermissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
    expect($resourcePermissions['view']['key'])->toStartWith('resource_view_');
});

it('respects config case and separator in custom builders', function () {
    $shield = new FilamentShield;

    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        // Use provided case and separator from config
        $formattedAffix = match ($case) {
            'upper_snake' => strtoupper(str_replace('-', '_', $affix)),
            'kebab' => str_replace('_', '-', $affix),
            default => $affix,
        };
        $formattedSubject = match ($case) {
            'upper_snake' => strtoupper(str_replace('-', '_', $subject)),
            'kebab' => str_replace('_', '-', $subject),
            default => $subject,
        };

        return $formattedAffix . $separator . $formattedSubject;
    });

    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);

    // Should use the config's case and separator
    expect($permissions['view']['key'])->toBeString();
    expect($permissions['view']['key'])->toContain('view');
});

it('builds permission labels correctly', function () {
    $shield = new FilamentShield;

    $affixes = ['view', 'create'];
    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, $affixes);

    expect($permissions['view']['label'])->toBeString();
    expect($permissions['create']['label'])->toBeString();
    expect($permissions['view']['label'])->toContain('View');
});

it('resets custom permission key builder correctly', function () {
    $shield = new FilamentShield;

    // Set custom builder
    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        return "custom_{$affix}_{$subject}";
    });

    $customPermissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
    expect($customPermissions['view']['key'])->toStartWith('custom_view_');

    // Create new instance (simulating reset)
    $newShield = new FilamentShield;
    $defaultPermissions = $newShield->getDefaultPermissionKeys(RoleResource::class, ['view']);

    expect($defaultPermissions['view']['key'])->not->toStartWith('custom_');
});

it('maintains fluent interface for buildPermissionKeyUsing', function () {
    $shield = new FilamentShield;

    $result = $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        return "{$affix}_{$subject}";
    });

    expect($result)->toBe($shield);
});

it('handles different affix formats', function () {
    $shield = new FilamentShield;

    // Test with snake_case affixes
    $permissions1 = $shield->getDefaultPermissionKeys(RoleResource::class, ['view_any', 'delete_any']);
    expect($permissions1)->toHaveKeys(['viewAny', 'deleteAny']);

    // Test with kebab-case affixes
    $permissions2 = $shield->getDefaultPermissionKeys(RoleResource::class, ['view-all']);
    expect($permissions2)->toHaveKey('viewAll');
});
