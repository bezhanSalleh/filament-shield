<?php

declare(strict_types=1);

/**
 * Example usage tests for the buildPermissionKeyUsing functionality
 */

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;

it('example: creates custom permission keys with prefixes', function () {
    $shield = new FilamentShield;

    // Example: Add a custom prefix to all permission keys
    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        return "myapp_{$affix}_{$subject}";
    });

    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view', 'create']);

    expect($permissions['view']['key'])->toStartWith('myapp_view_');
    expect($permissions['create']['key'])->toStartWith('myapp_create_');
});

it('example: creates different formats based on entity type', function () {
    $shield = new FilamentShield;

    // Example: Different naming conventions for different entity types
    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        if (str_contains($entity, 'Resource')) {
            return "admin.{$affix}.{$subject}"; // dot notation for resources
        }
        if (str_contains($entity, 'Page')) {
            return "page-{$affix}-{$subject}"; // kebab-case for pages
        }

        return "{$affix}_{$subject}"; // default underscore
    });

    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);
    expect($permissions['view']['key'])->toStartWith('admin.view.');
});

it('example: respects existing configuration', function () {
    $shield = new FilamentShield;

    // Example: Use the existing case and separator from config, but add custom logic
    $shield->buildPermissionKeyUsing(function ($entity, $affix, $subject, $case, $separator) {
        // Get the class name for more specific permissions
        $entityName = class_basename($entity);

        // Format using the provided case and separator from config
        $formattedAffix = match ($case) {
            'kebab' => str_replace('_', '-', $affix),
            'upper_snake' => strtoupper(str_replace('-', '_', $affix)),
            default => $affix,
        };

        $formattedSubject = match ($case) {
            'kebab' => str_replace('_', '-', strtolower($entityName)),
            'upper_snake' => strtoupper(str_replace('-', '_', $entityName)),
            default => strtolower($entityName),
        };

        return $formattedAffix . $separator . $formattedSubject;
    });

    $permissions = $shield->getDefaultPermissionKeys(RoleResource::class, ['view']);

    // Should use the entity class name instead of just the subject
    expect($permissions['view']['key'])->toContain('view');
    expect($permissions['view']['key'])->toContain('roleresource'); // or similar format
});
