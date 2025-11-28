<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Név',
    'column.guard_name' => 'Guard név',
    'column.roles' => 'Jogosultságok',
    'column.permissions' => 'Engedélyek',
    'column.updated_at' => 'Frissítve',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Név',
    'field.guard_name' => 'Guard név',
    'field.permissions' => 'Engedélyek',
    'field.select_all.name' => 'Összes kijelölése',
    'field.select_all.message' => 'Engedélyezze az összes jelenleg <span class="text-primary font-medium">bekapcsolt</span> engedélyt a szerepkör számára.',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Rendszer',
    'nav.role.label' => 'Jogosultságok',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Jogosultság',
    'resource.label.roles' => 'Jogosultságok',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entitások',
    'resources' => 'Erőforrások',
    'widgets' => 'Widgetek',
    'pages' => 'Oldalak',
    'custom' => 'Egyedi jogosultságok',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Nincs megfelelő hozzáférésed',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Megtekintés',
        'view_any' => 'Összes megtekintése',
        'create' => 'Létrehozás',
        'update' => 'Módosítás',
        'delete' => 'Törlés',
        'delete_any' => 'Összes törlése',
        'force_delete' => 'Végleges törlés',
        'force_delete_any' => 'Összes végeles törlése',
        'restore' => 'Helyreállítás',
        'reorder' => 'Sorbarendezés',
        'restore_any' => 'Összes helyreállítása',
        'replicate' => 'Másolás',
    ],
];
