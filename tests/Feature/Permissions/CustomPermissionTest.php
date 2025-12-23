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

it('returns empty array when custom permissions config is empty', function () {
    $shield = new FilamentShield;

    config(['filament-shield.custom_permissions' => []]);

    $result = $shield->transformCustomPermissions();

    expect($result)->toBe([]);
});

it('returns empty array when custom permissions config is not set', function () {
    $shield = new FilamentShield;

    config(['filament-shield.custom_permissions' => null]);

    $result = $shield->transformCustomPermissions();

    expect($result)->toBe([]);
});

it('transforms custom permissions with string keys correctly', function () {
    $shield = new FilamentShield;

    config([
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
            'view_reports' => 'View Reports',
            'export_data' => 'Export Data',
        ],
    ]);

    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(3);

    expect($result)->toHaveKey('ManageUsers');
    expect($result)->toHaveKey('ViewReports');
    expect($result)->toHaveKey('ExportData');

    expect($result['ManageUsers'])->toBe('Manage Users');
    expect($result['ViewReports'])->toBe('View Reports');
    expect($result['ExportData'])->toBe('Export Data');
});

it('transforms custom permissions with numeric keys correctly', function () {
    $shield = new FilamentShield;

    config([
        'filament-shield.custom_permissions' => [
            0 => 'manage_users',
            1 => 'view_reports',
            2 => 'export_data',
        ],
    ]);

    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(3);

    expect($result)->toHaveKey('ManageUsers');
    expect($result)->toHaveKey('ViewReports');
    expect($result)->toHaveKey('ExportData');

    expect($result['ManageUsers'])->toBe('Manage Users');
    expect($result['ViewReports'])->toBe('View Reports');
    expect($result['ExportData'])->toBe('Export Data');
});

it('uses localized labels when localizedOrFormatted is true', function () {
    $shield = new FilamentShield;

    config([
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
            'view_reports' => 'View Reports',
        ],
    ]);

    $result = $shield->transformCustomPermissions(true);

    expect($result)->toBeArray();
    expect($result)->toHaveCount(2);

    expect($result['ManageUsers'])->toBe('Manage Users');
    expect($result['ViewReports'])->toBe('View Reports');
});

it('respects different permission case formats', function () {
    config([
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
        ],
        'filament-shield.permissions.case' => 'kebab',
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(1);

    $keys = array_keys($result);
    $firstKey = $keys[0];
    expect($firstKey)->toBeString();
    expect($result[$firstKey])->toBe('Manage Users');
});

it('handles snake_case permission format', function () {
    config([
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
        ],
        'filament-shield.permissions.case' => 'snake',
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(1);

    $keys = array_keys($result);
    $firstKey = $keys[0];
    expect($firstKey)->toBeString();
    expect($result[$firstKey])->toBe('Manage Users');
});

it('handles upper_snake permission format', function () {
    config([
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
        ],
        'filament-shield.permissions.case' => 'upper_snake',
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(1);

    $keys = array_keys($result);
    $firstKey = $keys[0];
    expect($firstKey)->toBeString();
    expect($result[$firstKey])->toBe('Manage Users');
});

it('handles camelCase permission format', function () {
    config([
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
        ],
        'filament-shield.permissions.case' => 'camel',
    ]);

    $shield = new FilamentShield;
    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(1);

    $keys = array_keys($result);
    $firstKey = $keys[0];
    expect($firstKey)->toBeString();
    expect($result[$firstKey])->toBe('Manage Users');
});

it('handles mixed key types in custom permissions', function () {
    $shield = new FilamentShield;

    config([
        'filament-shield.custom_permissions' => [
            'manage_users' => 'Manage Users',
            0 => 'view_reports',
            'export_data' => 'Export Data',
            1 => 'delete_records',
        ],
    ]);

    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(4);

    expect($result)->toHaveKey('ManageUsers');
    expect($result)->toHaveKey('ExportData');

    expect($result)->toHaveKey('ViewReports');
    expect($result)->toHaveKey('DeleteRecords');
});

it('handles empty labels gracefully', function () {
    $shield = new FilamentShield;

    config([
        'filament-shield.custom_permissions' => [
            'test_permission' => '',
            0 => '',
        ],
    ]);

    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(2);

    expect($result)->toHaveKey('TestPermission');
    expect($result['TestPermission'])->toBe('');
});

it('handles special characters in permission names', function () {
    $shield = new FilamentShield;

    config([
        'filament-shield.custom_permissions' => [
            'manage-users-2' => 'Manage Users 2',
            'view_reports_2024' => 'View Reports 2024',
            'export-data-v2' => 'Export Data v2',
        ],
    ]);

    $result = $shield->transformCustomPermissions();

    expect($result)->toBeArray();
    expect($result)->toHaveCount(3);

    expect($result)->toHaveKey('ManageUsers2');
    expect($result)->toHaveKey('ViewReports2024');
    expect($result)->toHaveKey('ExportDataV2');
});
