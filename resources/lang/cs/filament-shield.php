<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Název',
    'column.guard_name' => 'Název guardu',
    'column.roles' => 'Role',
    'column.permissions' => 'Oprávnění',
    'column.updated_at' => 'Změněno dne',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Název',
    'field.guard_name' => 'Název guardu',
    'field.permissions' => 'Oprávnění',
    'field.select_all.name' => 'Vybrat vše',
    'field.select_all.message' => 'Povolit všechny oprávnení právě <span class="text-primary font-medium">Dostupné</span> pro tuto roli',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Role',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Role',
    'resource.label.roles' => 'Role',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entity',
    'resources' => 'Zdroje',
    'widgets' => 'Widgety',
    'pages' => 'Stránky',
    'custom' => 'Vlastní oprávnění',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Nemáte oprávnění k přístupu',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Zobrazit',
        'view_any' => 'Zobrazit jakýkoliv',
        'create' => 'Vyvořit',
        'update' => 'Upravit',
        'delete' => 'Smazat',
        'delete_any' => 'Smazat jakýkoliv',
        'force_delete' => 'Trvale smazat',
        'force_delete_any' => 'Trvale smazat jakýkoliv',
        'restore' => 'Obnovit',
        'reorder' => 'Změnit pořadí',
        'restore_any' => 'Obnovit jakýkoliv',
        'replicate' => 'Duplikovat',
    ],
];
