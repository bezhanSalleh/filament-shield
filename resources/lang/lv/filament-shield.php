<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nosaukums',
    'column.guard_name' => 'Sargs',
    'column.roles' => 'Lomas',
    'column.permissions' => 'Tiesības',
    'column.updated_at' => 'Atjaunots',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nosaukums',
    'field.guard_name' => 'Sargs',
    'field.permissions' => 'Tiesības',
    'field.select_all.name' => 'Atzīmēt visu',
    'field.select_all.message' => 'Aktivizēt visas <span class="text-primary font-medium">pieejamās</span> tiesības šai lomai',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Tiesības',
    'nav.role.label' => 'Lomas',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Loma',
    'resource.label.roles' => 'Lomas',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Vienības',
    'resources' => 'Resursi',
    'widgets' => 'Logrīki',
    'pages' => 'Lapas',
    'custom' => 'Speciālās tiesības',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Jums nav pietiekamu tiesību šī resursa apskatei.',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Skatīt',
        'view_any' => 'Skatīt visu',
        'create' => 'Izveidot',
        'update' => 'Atjaunot',
        'delete' => 'Dzēst',
        'delete_any' => 'Dzēst visu',
        'force_delete' => 'Piespiedu dzēšana',
        'force_delete_any' => 'Piespiedu dzēšana visam',
        'restore' => 'Atjaunot',
        'reorder' => 'Pārkārtot',
        'restore_any' => 'Atjaunot visu',
        'replicate' => 'Replicēt',
    ],
];
