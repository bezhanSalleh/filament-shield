<?php

return [
    'shield_resource' => [
        'slug' => 'shield/roles',
        'navigation_sort' => -1,
        'navigation_badge' => true,
        'navigation_group' => true,
        'is_globally_searchable' => false,
    ],

    'auth_provider_model' => [
        'fqcn' => 'App\\Models\\User',
    ],

    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
        'define_via_gate' => false,
        'intercept_gate' => 'before', // after
    ],

    'filament_user' => [
        'enabled' => true,
        'name' => 'filament_user',
    ],

    'permission_prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ],

        'page' => 'page',
        'widget' => 'widget',
    ],

    'entities' => [
        'pages' => true,
        'widgets' => true,
        'resources' => true,
        'custom_permissions' => false,
    ],

    'generator' => [
        'option' => 'policies_and_permissions',
    ],

    'models' =>  [
        'role' => \Spatie\Permission\Models\Role::class,
        'permission' => \Spatie\Permission\Models\Permission::class,
    ],

    'exclude' => [
        'enabled' => true,

        'pages' => [
            'Dashboard',
        ],

        'widgets' => [
            'AccountWidget', 'FilamentInfoWidget',
        ],

        'resources' => [],
    ],

    'register_role_policy' => [
        'enabled' => true,
    ],
];
