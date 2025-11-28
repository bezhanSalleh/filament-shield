<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Emri',
    'column.guard_name' => 'Emri i rojes',
    'column.roles' => 'Rolet',
    'column.permissions' => 'Lejet',
    'column.updated_at' => 'Përditësuar në',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Emri',
    'field.guard_name' => 'Emri i rojes',
    'field.permissions' => 'Lejet',
    'field.select_all.name' => 'Zgjidh të gjitha',
    'field.select_all.message' => 'Aktivizo të gjitha lejet aktualisht <span class="text-primary font-medium">Aktivizuar</span> për këtë rol',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rolet',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Rolet',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Seksioni',
    'resources' => 'Burimet',
    'widgets' => 'Widgets',
    'pages' => 'Faqet',
    'custom' => 'Lejet e personalizuara',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Nuk ke leje për të hyrë',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Shiko',
        'view_any' => 'Shiko çdo',
        'create' => 'Krijo',
        'update' => 'Përditëso',
        'delete' => 'Fshi',
        'delete_any' => 'Fshi çdo',
        'force_delete' => 'Fshije me forcë',
        'force_delete_any' => 'Fshije me forcë çdo',
        'restore' => 'Rikthe',
        'reorder' => 'Rirendit',
        'restore_any' => 'Rikthe çdo',
        'replicate' => 'Ripërsërit',
    ],
];
