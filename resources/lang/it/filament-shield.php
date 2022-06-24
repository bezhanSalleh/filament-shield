<?php

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

    /**
     * Role Setting Page
     */
    'page' => [
        'name' => 'Setting',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Salva',
        'generate' => 'Salva & Genera'
    ],
    'labels.super_admin.toggle_input' => 'Ruolo Super Admin',
    'labels.super_admin.text_input' => 'Nome Ruolo',
    'labels.filament_user.toggle_input' => 'Ruolo Utente Filament',
    'labels.filament_user.text_input' => 'Nome Ruolo',
    'labels.role_policy.toggle_input' => 'Ruolo Policy Registrato?',
    'labels.role_policy.message' => 'Assicurati che la policy sia registrato e i permessi applicati',
    'labels.prefixes.placeholder' => 'Prefisso Permesso di Default',
    'labels.prefixes.resource' => 'Resource',
    'labels.prefixes.resource.placeholder' => 'Aggiungi o Rimuovi Permessi alla Resource...',
    'labels.prefixes.page' => 'Page',
    'labels.prefixes.widget' => 'Widget',
    'labels.entities.placeholder' => 'Entity Permission Generators & Tabs',
    'labels.entities.message' => 'Generators & Tabs are ',
    'labels.entities.resources' => 'Resources',
    'labels.entities.pages' => 'Pages',
    'labels.entities.widgets' => 'Widgets',
    'labels.entities.custom_permissions' => 'Permessi Personalizzati',
    'labels.entities.custom_permissions.message' => 'il Tab è ',
    'labels.status.enabled' => 'Attivato',
    'labels.status.disabled' => 'Disattivato',
    'labels.status.yes' => 'Si',
    'labels.status.no' => 'No',
    'labels.exclude.placeholder' => 'Modalità di esclusione',
    'labels.exclude.message' => 'Abilitando la modalità di esclusione puoi indicare al generatore di autorizzazioni di saltare la creazione di autorizzazioni per le entità selezionate.',
    'labels.exclude.resources' => 'Resources',
    'labels.exclude.resources.placeholder' => 'Seleziona resources ...',
    'labels.exclude.pages' => 'Pages',
    'labels.exclude.pages.placeholder' => 'Seleziona pages ...',
    'labels.exclude.widgets' => 'Widgets',
    'labels.exclude.widgets.placeholder' => 'Seleziona widgets ...',

    /**
     * Messages
     */
    'forbidden' => 'Non hai i permessi di accesso',
    'update' => 'Renewed Shield\'s Config!',
    'generate' => 'Renewed Shield\'s Config & Generated Permissions w/o Policies!',
];
