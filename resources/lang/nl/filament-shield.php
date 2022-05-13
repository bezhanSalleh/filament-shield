<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Naam',
    'column.guard_name' => 'Guard Naam',
    'column.roles' => 'Rollen',
    'column.permissions' => 'Permissies',
    'column.updated_at' => 'Aangepast op',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Naam',
    'field.guard_name' => 'Guard Naam',
    'field.permissions' => 'Permissies',
    'field.select_all.name' => 'Selecteer alles',
    'field.select_all.message' => 'Zet alle permissies aan, die momenteel <span class="text-primary font-medium">aangevinkt</span> staan voor deze rol.',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rollen',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Rollen',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */
    'section' => 'Entiteiten',
    'resources' => 'Resources',
    'widgets' => 'Widgets',
    'pages' => 'Pagina\'s',
    'custom' => 'Andere permissies',

    /**
     * Role Setting Page
     */
    'page' => [
        'name' => 'Instelling',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Bewaar',
        'generate' => 'Bewaar & Genereer'
    ],
    'labels.super_admin.toggle_input' => 'Super Admin Rol',
    'labels.super_admin.text_input' => 'Rolnaam',
    'labels.filament_user.toggle_input' => 'Filament Gebruikersrol',
    'labels.filament_user.text_input' => 'Rolnaam',
    'labels.role_policy.toggle_input' => 'Rol Policy geregistreerd?',
    'labels.role_policy.message' => 'Zorg er voor dat de policy geregistreerd is en de permissies correct toegepast worden.',
    'labels.prefixes.placeholder' => 'Standaard voorvoegsels van de permissies',
    'labels.prefixes.resource' => 'Resource',
    'labels.prefixes.resource.placeholder' => 'Voeg toe of verwijder Resource permissies...',
    'labels.prefixes.page' => 'Pagina',
    'labels.prefixes.widget' => 'Widget',
    'labels.entities.placeholder' => 'Entity Permissie Generators & Tabs',
    'labels.entities.message' => 'Generators & Tabs zijn ',
    'labels.entities.resources' => 'Resources',
    'labels.entities.pages' => 'Pagina\'s',
    'labels.entities.widgets' => 'Widgets',
    'labels.entities.custom_permissions' => 'Andere Permissies',
    'labels.entities.custom_permissions.message' => 'Tab is ',
    'labels.status.enabled' => 'Aan',
    'labels.status.disabled' => 'Uit',
    'labels.status.yes' => 'Ja',
    'labels.status.no' => 'Neen',
    'labels.exclude.placeholder' => 'Exclusie Modus',
    'labels.exclude.message' => 'Door de Exclusie Modus aan te zetten, genereert de permissie generator geen permissies voor de geselecteerde entiteiten.',
    'labels.exclude.resources' => 'Resources',
    'labels.exclude.resources.placeholder' => 'Selecteer resources ...',
    'labels.exclude.pages' => 'Pagina\'s',
    'labels.exclude.pages.placeholder' => 'Selecteer pagina\'s ...',
    'labels.exclude.widgets' => 'Widgets',
    'labels.exclude.widgets.placeholder' => 'Selecteer widgets ...',

    /**
     * Messages
     */
    'forbidden' => 'Je hebt geen toegang',
    'update' => 'Shield Config is aangepast!',
    'generate' => 'Shield Config is aangepast en de permissions zijn gegenereerd zonder de policies!',
];
