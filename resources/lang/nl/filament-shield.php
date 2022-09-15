<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Naam',
    'column.guard_name' => 'Guard Naam',
    'column.roles' => 'Rollen',
    'column.permissions' => 'Permissies',
    'column.updated_at' => 'Aangepast op',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Naam',
    'field.guard_name' => 'Guard Naam',
    'field.permissions' => 'Permissies',
    'field.select_all.name' => 'Selecteer alles',
    'field.select_all.message' => 'Zet alle permissies aan, die momenteel <span class="text-primary font-medium">aangevinkt</span> staan voor deze rol.',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rollen',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Rollen',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entiteiten',
    'resources' => 'Resources',
    'widgets' => 'Widgets',
    'pages' => 'Pagina\'s',
    'custom' => 'Andere permissies',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Je hebt geen toegang',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Bekijken',
        'view_any' => 'Bekijk elke',
        'create' => 'Aanmaken',
        'update' => 'Bewerken',
        'delete' => 'Verwijderen',
        'delete_any' => 'Verwijder elke',
        'force_delete' => 'Forceer verwijderen',
        'force_delete_any' => 'Forceer verwijderen elke',
        'restore' => 'Herstellen',
        'restore_any' => 'Herstel elke',
        'replicate' => 'Repliceren',
        // 'reorder' => 'Reorder',
    ],
];
