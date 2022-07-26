<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tabel kolommen
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Naam',
    'column.guard_name' => 'Guard naam',
    'column.roles' => 'Rollen',
    'column.permissions' => 'Permissies',
    'column.updated_at' => 'Bijgewerkt op',

    /*
    |--------------------------------------------------------------------------
    | Form velden
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Naam',
    'field.guard_name' => 'Guard naam',
    'field.permissions' => 'Permissies',
    'field.select_all.name' => 'Selecteer alles',
    'field.select_all.message' => 'Schakel alle permissies in die <span class="text-primary font-medium">ingeschakeld</span> zijn voor deze rol',

    /*
    |--------------------------------------------------------------------------
    | Navigatie & resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rollen',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Rollen',

    /*
    |--------------------------------------------------------------------------
    | Secties & tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entiteiten',
    'resources' => 'Resources',
    'widgets' => 'Widgets',
    'pages' => 'Pagina\'s',
    'custom' => 'Andere permissies',

    /*
    |--------------------------------------------------------------------------
    | Shield instellingen pagina
    |--------------------------------------------------------------------------
    */

    'page' => [
        'name' => 'Instellingen',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Opslaan',
        'generate' => 'Opslaan & genereren',
        'load_default_settings' => 'Laad standaard instellingen',
    ],

    'settings' => [
        'enabled' =>  false,
        'label' => 'Instellingen pagina',
        'helper_text' => 'Schakel de instellingen pagina in of uit. Alleen beschikbaar voor super admins.',
        'navigation_label' => 'Shield instellingen',

        'generator_options' => [
            'policies_and_permissions' => 'Genereer policies & permissies',
            'policies' => 'Genereer alleen policies',
            'permissions' => 'Genereer alleen permissies',
        ],

        'auth_provider' => [
            'label' => 'Auth Provider Model',
            'helper_text' => 'Volledig gekwalificeerde classname van het model gebruikt om policies te genereren.'
        ],

        'resource' => [
            'name' => 'Shield rol resource',
            'slug' => 'Url',
            'navigation_sort' => 'Navigatie positie',
        ]
    ],


    /*
    |--------------------------------------------------------------------------
    | Labels
    |--------------------------------------------------------------------------
    */

    'labels.super_admin.toggle_input' => 'Super admin Rol',
    'labels.super_admin.text_input' => 'Rolnaam',
    'labels.filament_user.toggle_input' => 'Filament gebruikersrol',
    'labels.filament_user.text_input' => 'Rolnaam',
    'labels.role_policy.toggle_input' => 'Rol policy geregistreerd?',
    'labels.role_policy.message' => 'Zorgt er voor dat het beleid geregistreerd is en de permissies correct toegepast worden.',
    'labels.permission_prefixes.placeholder' => 'Standaar permissie voorvoegsel',
    'labels.permission_prefixes.resource' => 'Resource',
    'labels.permission_prefixes.resource.placeholder' => 'Voeg toe of verwijder resource permissies...',
    'labels.permission_prefixes.page' => 'Pagina',
    'labels.permission_prefixes.widget' => 'Widget',
    'labels.entities.placeholder' => 'Entiteit permissies generators & tabs',
    'labels.entities.message' => 'Generators & Tabs zijn ',
    'labels.entities.resources' => 'Resources',
    'labels.entities.pages' => 'Pagina\'s',
    'labels.entities.widgets' => 'Widgets',
    'labels.entities.custom_permissions' => 'Eigen permissies',
    'labels.entities.custom_permissions.message' => 'Tab is ',
    'labels.status.enabled' => 'Ingeschakeld',
    'labels.status.disabled' => 'Uitgeschakeld',
    'labels.status.yes' => 'Ja',
    'labels.status.no' => 'Nee',
    'labels.exclude.placeholder' => 'Exclusie modus',
    'labels.exclude.message' => 'Door de Exclusie Modus aan te zetten, genereert de permissie generator geen permissies voor de geselecteerde entiteiten.',
    'labels.exclude.resources' => 'Resources',
    'labels.exclude.resources.placeholder' => 'Selecteer resources ...',
    'labels.exclude.pages' => 'Pagina\'s',
    'labels.exclude.pages.placeholder' => 'Selecteer pagina\'s ...',
    'labels.exclude.widgets' => 'Widgets',
    'labels.exclude.widgets.placeholder' => 'Selecteer widgets ...',

    /*
    |--------------------------------------------------------------------------
    | Meldingen
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Je hebt geen toegang',
    'update' => 'Shield Config is aangepast!',
    'generate' => 'Shield config bijgewerkt & permissies gegenereerd en/of policies gebaseerd op de nieuwe instellingen.',
    'loaded_default_settings' => 'Shield standaard instellingen geladen.',

    /*
    |--------------------------------------------------------------------------
    | Resource permissies labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Bekijken',
        'view_any' => 'Bekijk elke',
        'create' => 'Aanmaken',
        'update' => 'Bewerken',
        'delete' => 'Verwijderen',
        'delete_any' => 'Verwijder elke',
        'force_delete' => 'Forceer verwijderen',
        'force_delete_any' => 'Forceer verwijderen elke',
        'restore' => 'Herstellen',
        'restore_any' => 'Herstel elke',
        'replicate' => 'Repliceren',
    ],
];
