<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nazwa',
    'column.guard_name' => 'Nazwa strażnika',
    'column.roles' => 'Role',
    'column.permissions' => 'Permisje',
    'column.updated_at' => 'Zaktualizowano',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nazwa',
    'field.guard_name' => 'Nzwa strażnika',
    'field.permissions' => 'Permisje',
    'field.select_all.name' => 'Zaznacz wszystkie',
    'field.select_all.message' => 'Włącz wszystkie permisje obecnie<span class="text-primary font-medium">Włączone</span> dla tej roli',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Role',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rola',
    'resource.label.roles' => 'Role',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Podmioty',
    'resources' => 'Zasoby',
    'widgets' => 'Widżety',
    'pages' => 'Strony',
    'custom' => 'Niestandardowe permisje',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Nie masz uprawnień do dostępu',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Widok',
        'view_any' => 'Widok dowolnego',
        'create' => 'Tworzenie',
        'update' => 'Aktualizacja',
        'delete' => 'Usuwanie',
        'delete_any' => 'Usuwanie dowolnego',
        'force_delete' => 'Wymuszone usunięcie',
        'force_delete_any' => 'Wymuszone usunięcie dowolnego',
        'restore' => 'Przywracanie',
        'reorder' => 'Zmiana kolejności',
        'restore_any' => 'Przywracanie dowolnego',
        'replicate' => 'Duplikowanie',
    ],
];
