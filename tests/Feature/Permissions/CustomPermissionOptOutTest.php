<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShield;

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
// format_custom_permission_keys = false (opt-out)
// ──────────────────────────────────────────────────────────

it('preserves custom permission keys as-is when formatting is disabled', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.permissions.format_custom_permission_keys' => false,
        'filament-shield.custom_permissions' => [
            'terraform.view_system_logs' => 'View System Logs',
            'keycloak:manage-users' => 'Manage Users',
            'custom_permission_key' => 'Custom Permission',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    // Keys must be exactly as defined — no case conversion
    expect($result)->toHaveKey('terraform.view_system_logs');
    expect($result)->toHaveKey('keycloak:manage-users');
    expect($result)->toHaveKey('custom_permission_key');
});

it('preserves numeric-keyed permission keys as-is when formatting is disabled', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.permissions.format_custom_permission_keys' => false,
        'filament-shield.custom_permissions' => [
            'view_system_logs',
            'manage-users',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('view_system_logs');
    expect($result)->toHaveKey('manage-users');
});

it('formats custom permission keys when formatting is enabled (default)', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.permissions.format_custom_permission_keys' => true,
        'filament-shield.custom_permissions' => [
            'view_system_logs' => 'View System Logs',
            'manage-users' => 'Manage Users',
        ],
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('ViewSystemLogs');
    expect($result)->toHaveKey('ManageUsers');
});

it('defaults to formatting when format_custom_permission_keys is not set', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'view_system_logs' => 'View System Logs',
        ],
    ]);

    // Explicitly remove the key to test the default
    config(['filament-shield.permissions.format_custom_permission_keys' => null]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('ViewSystemLogs');
});

// ──────────────────────────────────────────────────────────
// buildPermissionKeyUsing closure with custom permissions
// ──────────────────────────────────────────────────────────

it('routes custom permissions through buildPermissionKeyUsing closure', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
        ],
    ]);

    $shield = new FilamentShield;

    $capturedParams = [];
    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) use (&$capturedParams) {
        $capturedParams = compact('entity', 'affix', 'subject', 'case', 'separator');

        return 'custom_closure_result';
    });

    $result = $shield->transformCustomPermissions();

    expect($capturedParams['entity'])->toBe('custom');
    expect($capturedParams['affix'])->toBeNull();
    expect($capturedParams['subject'])->toBe('manage_users');
    expect($capturedParams['case'])->toBe('pascal');
    expect($capturedParams['separator'])->toBe(':');
    expect($result)->toHaveKey('custom_closure_result');
});

it('falls back to default formatting when closure returns null for custom permissions', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
        ],
    ]);

    $shield = new FilamentShield;

    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        if ($entity === 'custom') {
            return null; // let default handle it
        }

        return "overridden_{$affix}_{$subject}";
    });

    $result = $shield->transformCustomPermissions();

    // Should fall back to default format (pascal)
    expect($result)->toHaveKey('ManageUsers');
});

it('allows closure to return raw key for custom while transforming resources', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.custom_permissions' => [
            'keycloak:manage-users' => 'Manage Users',
        ],
    ]);

    $shield = new FilamentShield;

    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        if ($entity === 'custom') {
            return $subject; // use as-is for custom
        }

        return null; // default for everything else
    });

    $result = $shield->transformCustomPermissions();

    expect($result)->toHaveKey('keycloak:manage-users');
});

it('closure takes priority over format_custom_permission_keys config', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.permissions.format_custom_permission_keys' => false,
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
        ],
    ]);

    $shield = new FilamentShield;

    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        if ($entity === 'custom') {
            return 'closure_wins';
        }

        return null;
    });

    $result = $shield->transformCustomPermissions();

    // Closure return takes priority even when formatting is disabled
    expect($result)->toHaveKey('closure_wins');
});

it('respects format_custom_permission_keys when closure returns null', function () {
    config([
        'filament-shield.permissions.case' => 'pascal',
        'filament-shield.permissions.separator' => ':',
        'filament-shield.permissions.format_custom_permission_keys' => false,
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
        ],
    ]);

    $shield = new FilamentShield;

    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        return null; // fall back for everything
    });

    $result = $shield->transformCustomPermissions();

    // Closure returned null, formatting is disabled → raw key
    expect($result)->toHaveKey('manage_users');
});
