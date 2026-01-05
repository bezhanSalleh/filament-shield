<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nome',
    'column.guard_name' => 'Nome Guard',
    'column.roles' => 'Ruoli',
    'column.permissions' => 'Permessi',
    'column.updated_at' => 'Aggiornato a',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nome',
    'field.guard_name' => 'Nome Guard',
    'field.permissions' => 'Permessi',
    'field.select_all.name' => 'Seleziona Tutto',
    'field.select_all.message' => 'Abilita tutti i Permessi attualmente <span class="text-primary font-medium">Abilitati</span> per questo ruolo',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Ruoli',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Ruolo',
    'resource.label.roles' => 'Ruoli',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entities',
    'resources' => 'Resources',
    'widgets' => 'Widgets',
    'pages' => 'Pages',
    'custom' => 'Permessi Personalizzati',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Non hai i permessi di accesso',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

     'resource_permission_prefixes_labels' => [
         'view' => 'Visualizza',
         'view_any' => 'Visualizza tutti',
         'create' => 'Crea',
         'update' => 'Modifica',
         'delete' => 'Elimina',
         'delete_any' => 'Elimina tutti',
         'force_delete' => 'Elimina definitivamente',
         'force_delete_any' => 'Elimina definitivamente tutti',
         'restore' => 'Ripristina',
         'replicate' => 'Duplica',
         'reorder' => 'Riordina',
         'restore_any' => 'Ripristina tutti',
     ],
];
