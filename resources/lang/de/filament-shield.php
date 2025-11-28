<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.guard_name' => 'Guard-Name',
    'column.name' => 'Name',
    'column.permissions' => 'Berechtigungen',
    'column.roles' => 'Rollen',
    'column.updated_at' => 'Aktualisiert am',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.guard_name' => 'Guard-Name',
    'field.name' => 'Name',
    'field.permissions' => 'Berechtigungen',
    'field.select_all.message' => 'Aktivierung aller Berechtigungen, die derzeit für diese Rolle <span class="text-primary font-medium">aktiviert</span> sind',
    'field.select_all.name' => 'Alle auswählen',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'nav.role.label' => 'Rollen',
    'resource.label.role' => 'Rolle',
    'resource.label.roles' => 'Rollen',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entitäten',
    'resources' => 'Ressourcen',
    'widgets' => 'Widgets',
    'pages' => 'Seiten',
    'custom' => 'benutzerdefinierte Berechtigungen',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Sie haben keine Zugangsberechtigung',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Anzeigen',
        'view_any' => 'Alle anzeigen',
        'create' => 'Erstellen',
        'update' => 'Bearbeiten',
        'delete' => 'Löschen',
        'delete_any' => 'Alle löschen',
        'force_delete' => 'Endgültig löschen',
        'force_delete_any' => 'Alle endgültig löschen',
        // 'reorder' => 'Reorder',
        // 'replicate' => 'Replicate',
        'restore' => 'Wiederherstellen',
        'restore_any' => 'Alle wiederherstellen',
    ],
];
