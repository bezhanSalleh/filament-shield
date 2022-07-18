<?php

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
    | Role Setting Page
    |--------------------------------------------------------------------------
    */

    'page' => [
        'name' => 'Einstellungen',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Speichern',
        'generate' => 'Speichern & Erstellen',
        'load_default_settings' => 'Standardeinstellungen laden',
        'cancel' => 'Abbrechen',
    ],
    'labels.super_admin.toggle_input' => 'Super Admin Rolle',
    'labels.super_admin.text_input' => 'Rollenname',
    'labels.filament_user.toggle_input' => 'Filament User Rolle',
    'labels.filament_user.text_input' => 'Rollenname',
    'labels.role_policy.toggle_input' => 'Rollenrichtlinie registriert?',
    'labels.role_policy.message' => 'Sicherstellen, dass die Richtlinie registriert ist und die Berechtigungen durchgesetzt werden',
    'labels.permission_prefixes.placeholder' => 'Präfixe Standardberechtigungen',
    'labels.permission_prefixes.resource' => 'Resource',
    'labels.permission_prefixes.resource.placeholder' => 'Ressourcenberechtigungen bearbeiten ...',
    'labels.permission_prefixes.page' => 'Page',
    'labels.permission_prefixes.widget' => 'Widget',
    'labels.entities.placeholder' => 'Generatoren für Entitätsberechtigungen und Registerkarten',
    'labels.entities.message' => 'Generatoren und Registerkarten sind',
    'labels.entities.resources' => 'Ressourcen',
    'labels.entities.pages' => 'Seiten',
    'labels.entities.widgets' => 'Widgets',
    'labels.entities.custom_permissions' => 'Benutzerdefinierte Berechtigungen',
    'labels.entities.custom_permissions.message' => 'Registerkarte ist ',
    'labels.status.enabled' => 'eingeschaltet',
    'labels.status.disabled' => 'ausgeschaltet',
    'labels.status.yes' => 'Ja',
    'labels.status.no' => 'Nein',
    'labels.exclude.placeholder' => 'Ausschlussmodus',
    'labels.exclude.message' => 'Durch Aktivieren des Ausschlussmodus können Sie den Berechtigungsgenerator anweisen, die Erstellung von Berechtigungen für die von Ihnen ausgewählten Entitäten zu überspringen.',
    'labels.exclude.resources' => 'Ressourcen',
    'labels.exclude.resources.placeholder' => 'Ressourcen auswählen ...',
    'labels.exclude.pages' => 'Seiten',
    'labels.exclude.pages.placeholder' => 'Seiten auswählen ...',
    'labels.exclude.widgets' => 'Widgets',
    'labels.exclude.widgets.placeholder' => 'Widgets auswählen ...',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Sie haben keine Zugangsberechtigung',
    'update' => 'Shield Konfiguration aktualisiert!',
    'generate' => 'Shield Konfiguration aktualisiert & Berechtigungen bzw. Policies erzeugt!',
    'loaded_default_settings' => 'Shields Standardeinstellungen geladen.',

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
        'restore' => 'Wiederherstellen',
        'restore_any' => 'Alle wiederherstellen',
    ]
];
