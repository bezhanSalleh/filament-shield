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
// format() normalization: any input format → correct output
// ──────────────────────────────────────────────────────────

it('converts snake_case input to kebab-case', function () {
    config([
        'filament-shield.permissions.case' => 'kebab',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'view_single_record' => 'View Single Record',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('view-single-record');
});

it('converts UPPER_SNAKE input to kebab-case', function () {
    config([
        'filament-shield.permissions.case' => 'kebab',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'VIEW_SINGLE_RECORD' => 'View Single Record',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('view-single-record');
});

it('converts kebab-case input to snake_case', function () {
    config([
        'filament-shield.permissions.case' => 'snake',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'view-single-record' => 'View Single Record',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('view_single_record');
});

it('converts camelCase input to snake_case', function () {
    config([
        'filament-shield.permissions.case' => 'snake',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'viewSingleRecord' => 'View Single Record',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('view_single_record');
});

it('converts snake_case input to PascalCase', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'view_system_logs' => 'View System Logs',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('ViewSystemLogs');
});

it('converts snake_case input to UPPER_SNAKE_CASE', function () {
    config([
        'filament-shield.permissions.case' => 'upper_snake',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'force_delete_any' => 'Force Delete Any',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('FORCE_DELETE_ANY');
});

it('converts PascalCase input to kebab-case', function () {
    config([
        'filament-shield.permissions.case' => 'kebab',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'ForceDeleteAny' => 'Force Delete Any',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('force-delete-any');
});

it('normalizes consistently regardless of input format', function () {
    $inputs = [
        'view_single_record' => 'View Single Record',
        'view-single-record' => 'View Single Record',
        'viewSingleRecord' => 'View Single Record',
        'ViewSingleRecord' => 'View Single Record',
        'VIEW_SINGLE_RECORD' => 'View Single Record',
    ];

    $cases = ['snake', 'kebab', 'pascal', 'camel', 'upper_snake', 'lower_snake'];

    foreach ($cases as $case) {
        $results = [];

        foreach ($inputs as $input => $label) {
            config([
                'filament-shield.permissions.case' => $case,
                'filament-shield.permissions.separator' => ':',
                'filament-shield.custom_permissions' => [$input => $label],
            ]);

            $shield = new FilamentShield;
            $result = $shield->transformCustomPermissions();
            $results[] = array_keys($result)[0];
        }

        // All inputs should produce the same key for each case format
        expect(array_unique($results))->toHaveCount(1, "Case '{$case}' produced inconsistent results: " . implode(', ', $results));
    }
});

// ──────────────────────────────────────────────────────────
// Separator-aware formatting for custom permissions
// ──────────────────────────────────────────────────────────

it('formats each segment independently when custom permission contains separator', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'view:system_log' => 'View System Log',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('View:SystemLog');
});

it('formats separator-delimited custom permission in snake_case', function () {
    config([
        'filament-shield.permissions.case' => 'snake',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'View:SystemLog' => 'View System Log',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('view:system_log');
});

it('formats separator-delimited custom permission in kebab-case', function () {
    config([
        'filament-shield.permissions.case' => 'kebab',
        'filament-shield.permissions.separator' => '.',
        'filament-shield.custom_permissions' => [
            'force_delete_any.system_log' => 'Force Delete Any System Log',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('force-delete-any.system-log');
});

// ──────────────────────────────────────────────────────────
// Resource permission formatting also benefits from normalization
// ──────────────────────────────────────────────────────────

it('normalizes resource affixes regardless of input format', function () {
    config([
        'filament-shield.permissions.case' => 'snake',
        'filament-shield.permissions.separator' => ':',
    ]);

    $shield = new FilamentShield;

    // Build permissions separately to avoid key deduplication in mapWithKeys
    $permissions1 = $shield->getDefaultPermissionKeys(RoleResource::class, ['force_delete_any']);
    $permissions2 = $shield->getDefaultPermissionKeys(RoleResource::class, ['forceDeleteAny']);

    // Both input formats should produce the same permission key
    expect($permissions1['forceDeleteAny']['key'])->toBe($permissions2['forceDeleteAny']['key']);
});
