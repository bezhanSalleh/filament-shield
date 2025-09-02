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
    ],

    'policies' => [
        'path' => app_path('Policies'),
        'merge' => true, // when true this will merge the following default methods with the ones defined per resource in the resources.manage key
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

    'localization' => [
        'enabled' => false,
        'key' => 'filament-shield::filament-shield',
    ],

    'resources' => [
        'subject' => 'model', // class
        'manage' => [ // list of resources as keys(Fully Quilified Class Name) and array of affixes as values
            \BezhanSalleh\FilamentShield\Resources\Roles\RoleResource::class => [
                'viewAny',
                'view',
                'create',
                'update',
                'delete',
            ],
        ],
        'exclude' => [ // list of resources (fully qualified class names) to exclude
            //
        ],
    ],

    'pages' => [
        'subject' => 'class', // model if you page has $page::getModel(),
        'prefix' => 'view',
        'exclude' => [ // list of pages (fully qualified class names) to exclude
            \Filament\Pages\Dashboard::class,
        ],
    ],

    'widgets' => [
        'subject' => 'class', // model if you widget has $widget::getModel(),
        'prefix' => 'view',
        'exclude' => [ // list of widgets (fully qualified class names) to exclude
            \Filament\Widgets\AccountWidget::class,
            \Filament\Widgets\FilamentInfoWidget::class,
        ],
    ],

    'custom_permissions' => [],

    'discovery' => [
        'discover_all_resources' => false,
        'discover_all_widgets' => false,
        'discover_all_pages' => false,
    ],

    'register_role_policy' => true,

];
