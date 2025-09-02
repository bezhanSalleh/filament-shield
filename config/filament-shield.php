<?php

return [

    'shield_resource' => [
        'slug' => 'shield/roles',
        'show_model_path' => true,
        'cluster' => null,
        'tabs' => [
            'pages' => true,
            'widgets' => true,
            'resources' => true,
            'custom_permissions' => false,
        ],
    ],

    'tenant_model' => null,

    'auth_provider_model' => 'App\\Models\\User',

    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
        'define_via_gate' => false,
        'intercept_gate' => 'before', // after
    ],

    'panel_user' => [
        'enabled' => true,
        'name' => 'panel_user',
    ],

    'permissions' => [
        'separator' => ':',
        'case' => 'pascal', // sanke, kebab, pascal, upper_snake, lower_snake
        'generate' => true,
        'resource' => [
            'subject' => 'model', // class
            // prefix for resources are resolved from policy methods
        ],
        'page' => [
            'subject' => 'class', // model if you page has $page::getModel(),
            'prefix' => 'view',
        ],
        'widget' => [
            'subject' => 'class', // model if you widget has $widget::getModel(),
            'prefix' => 'view',
        ],
        'localization' => [
            'enabled' => false,
            'key' => 'filament-shield::filament-shield',
        ],
    ],

    'policies' => [
        'path' => app_path('Policies'),
        'merge' => true,
        'generate' => true,
        'methods' => [
            'viewAny',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'forceDelete',
            'replicate',
            'reorder',
            'viewAny',
        ],
        'single_parameter_methods' => [
            'viewAny',
            'create',
            'deleteAny',
            'forceDeleteAny',
            'restoreAny',
            'reorder',
        ],
    ],

    'generator' => [ // TODO: should be replaced now
        'option' => 'policies_and_permissions',
        'policy_directory' => 'Policies',
        'policy_namespace' => 'Policies',
    ],

    'exclude' => [

        'resources' => [],

        'pages' => [
            'Dashboard',
        ],

        'widgets' => [
            'AccountWidget', 'FilamentInfoWidget',
        ],

    ],

    'discovery' => [
        'discover_all_resources' => false,
        'discover_all_widgets' => false,
        'discover_all_pages' => false,
    ],

    'register_role_policy' => true,

    'custom_permissions' => [],

];
