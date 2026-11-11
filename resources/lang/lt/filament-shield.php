<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Vardas',
    'column.guard_name' => 'Apsaugos tipas',
    'column.team' => 'Komanda',
    'column.roles' => 'Rolės',
    'column.permissions' => 'Leidimai',
    'column.updated_at' => 'Atnaujinta',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Vardas',
    'field.guard_name' => 'Apsaugos tipas',
    'field.permissions' => 'Leidimai',
    'field.team' => 'Komanda',
    'field.team.placeholder' => 'Pasirinkite komandą ...',
    'field.select_all.name' => 'Pasirinkti visus',
    'field.select_all.message' => 'Įgalina arba išjungia visus šios rolės leidimus',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rolės',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rolė',
    'resource.label.roles' => 'Rolės',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Objektai',
    'resources' => 'Ištekliai',
    'widgets' => 'Valdikliai',
    'pages' => 'Puslapiai',
    'custom' => 'Pasirinktiniai leidimai',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Neturite prieigos teisės',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Peržiūra',
        'view_any' => 'Sąrašo peržiūra',
        'create' => 'Kūrimas',
        'update' => 'Atnaujinimas',
        'delete' => 'Ištrynimas',
        'delete_any' => 'Bet kurio ištrynimas',
        'force_delete' => 'Priverstinis ištrynimas',
        'force_delete_any' => 'Priverstinis bet kurio ištrynimas',
        'restore' => 'Atkūrimas',
        'reorder' => 'Rikiavimo keitimas',
        'restore_any' => 'Bet kurio atkūrimas',
        'replicate' => 'Kopijavimas',
    ],
];
