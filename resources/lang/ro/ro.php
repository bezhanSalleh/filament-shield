<?php

return [
    /*
    |------------------------------------------------- -------------------------
    | Table Columns
    |------------------------------------------------- -------------------------
    */

    'column.name' => 'Număr',
    'column.guard_name' => 'Numele paznicului',
    'column.roles' => 'Roluri',
    'column.permissions' => 'Permisiuni',
    'column.updated_at' => 'Actualizat la',

    /*
    |------------------------------------------------- -------------------------
    | Form Fields
    |------------------------------------------------- -------------------------
    */

    'field.name' => 'Nume',
    'field.guard_name' => 'Numele paznicului',
    'field.permissions' => 'Permisiuni',
    'field.select_all.name' => 'Selectați tot',
    'field.select_all.message' => 'Activați toate permisiunile în prezent <span class="text-primary font-medium">Activate</span> pentru acest rol',

    /*
    |------------------------------------------------- -------------------------
    | Navigation & Resources
    |------------------------------------------------- -------------------------
    */

    'nav.group' => 'Scut',
    'nav.role.label' => 'Roluri',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Roluri',

    /*
    |------------------------------------------------- -------------------------
    | Section & Tabs
    |------------------------------------------------- -------------------------
    */

    'section' => 'Entități',
    'resources' => 'Resurse',
    'widgets' => 'Widget-uri',
    'pages' => 'Pagini',
    'custom' => 'Permisiuni personalizate',

    /*
    |------------------------------------------------- -------------------------
    | Posts
    |------------------------------------------------- -------------------------
    */

    'forbidden' => 'Nu aveți permisiunea de a accesa',

    /*
    |------------------------------------------------- -------------------------
    | Resource Permissions' Labels
    |------------------------------------------------- -------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Vizualizare',
        'view_any' => 'Vedeți orice',
        'create' => 'Creează',
        'update' => 'Actualizare',
        'delete' => 'Șterge',
        'delete_any' => 'Șterge orice',
        'force_delete' => 'Forțat ștergerea',
        'force_delete_any' => 'Forțat ștergerea oricărei',
        'restore' => 'Restaurare',
        'reorder' => 'Reordonare',
        'restore_any' => 'Restaurează orice',
        'replicate' => 'Replicare',
    ],
];
