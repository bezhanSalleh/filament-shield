<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stĺpce tabuľky
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Meno',
    'column.guard_name' => 'Názov ochrany',
    'column.roles' => 'Roly',
    'column.permissions' => 'Oprávnenia',
    'column.updated_at' => 'Aktualizované',

    /*
    |--------------------------------------------------------------------------
    | Polia formulára
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Meno',
    'field.guard_name' => 'Názov ochrany',
    'field.permissions' => 'Oprávnenia',
    'field.select_all.name' => 'Vybrať všetko',
    'field.select_all.message' => 'Povoliť všetky oprávnenia aktuálne <span class="text-primary font-medium">Povolené</span> pre túto rolu',

    /*
    |--------------------------------------------------------------------------
    | Navigácia & Zdroje
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Roly',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rola',
    'resource.label.roles' => 'Roly',

    /*
    |--------------------------------------------------------------------------
    | Sekcie & Karty
    |--------------------------------------------------------------------------
    */

    'section' => 'Entity',
    'resources' => 'Zdroje',
    'widgets' => 'Widgety',
    'pages' => 'Stránky',
    'custom' => 'Vlastné oprávnenia',

    /*
    |--------------------------------------------------------------------------
    | Správy
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Nemáte oprávnenie na prístup',

    /*
    |--------------------------------------------------------------------------
    | Štítky oprávnení zdrojov
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Zobraziť',
        'view_any' => 'Zobraziť všetko',
        'create' => 'Vytvoriť',
        'update' => 'Aktualizovať',
        'delete' => 'Odstrániť',
        'delete_any' => 'Odstrániť všetko',
        'force_delete' => 'Natrvalo odstrániť',
        'force_delete_any' => 'Natrvalo odstrániť všetko',
        'restore' => 'Obnoviť',
        'reorder' => 'Preusporiadať',
        'restore_any' => 'Obnoviť všetko',
        'replicate' => 'Replikovať',
    ],
];
