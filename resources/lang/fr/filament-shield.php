<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nom',
    'column.guard_name' => 'Nom du Guard',
    'column.roles' => 'Rôles',
    'column.permissions' => 'Permissions',
    'column.updated_at' => 'Mis à jour à',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nom',
    'field.guard_name' => 'Nom du Guard',
    'field.permissions' => 'Permissions',
    'field.select_all.name' => 'Tout sélectionner',
    'field.select_all.message' => 'Activer toutes les autorisations pour ce rôle',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rôles',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rôle',
    'resource.label.roles' => 'Rôles',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Section',
    'resources' => 'Ressources',
    'widgets' => 'Widgets',
    'pages' => 'Pages',
    'custom' => 'Permissions personnalisées',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Vous n\'avez pas la permission d\'accéder',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Voir',
        'view_any' => 'Voir tout',
        'create' => 'Créer',
        'update' => 'Mettre à jour',
        'delete' => 'Supprimer',
        'delete_any' => 'Supprimer tout',
        'force_delete' => 'Forcer la suppression',
        'force_delete_any' => 'Forcer la suppression de tout',
        'restore' => 'Restaurer',
        'replicate' => 'Répliquer',
        'reorder' => 'Réordonner',
        'restore_any' => 'Restaurer tout',
    ],
];
