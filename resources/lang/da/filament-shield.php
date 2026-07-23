<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Navn',
    'column.guard_name' => 'Guard-navn',
    'column.team' => 'Team',
    'column.roles' => 'Roller',
    'column.permissions' => 'Rettigheder',
    'column.updated_at' => 'Opdateret den',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Navn',
    'field.guard_name' => 'Guard-navn',
    'field.permissions' => 'Rettigheder',
    'field.team' => 'Team',
    'field.team.placeholder' => 'Vælg et team ...',
    'field.select_all.name' => 'Vælg alle',
    'field.select_all.message' => 'Aktiverer/deaktiverer alle rettigheder for denne rolle',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Roller',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rolle',
    'resource.label.roles' => 'Roller',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entiteter',
    'resources' => 'Ressourcer',
    'widgets' => 'Widgets',
    'pages' => 'Sider',
    'custom' => 'Brugerdefinerede rettigheder',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Adgang nægtet',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Vis',
        'view_any' => 'Vis alle',
        'create' => 'Opret',
        'update' => 'Opdater',
        'delete' => 'Slet',
        'delete_any' => 'Slet alle',
        'force_delete' => 'Slet permanent',
        'force_delete_any' => 'Slet alle permanent',
        'restore' => 'Gendan',
        'reorder' => 'Sorter',
        'restore_any' => 'Gendan alle',
        'replicate' => 'Kopier',
    ],
];
