<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Ad',
    'column.guard_name' => 'Koruma Adı',
    'column.roles' => 'Roller',
    'column.permissions' => 'İzinler',
    'column.updated_at' => 'Güncellenme Tarihi',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Ad',
    'field.guard_name' => 'Koruma Adı',
    'field.permissions' => 'İzinler',
    'field.select_all.name' => 'Tümünü se.',
    'field.select_all.message' => 'Bu rol için tüm İzinleri etkinleştir',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Kalkan',
    'nav.role.label' => 'Roller',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Roller',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */
    'section' => 'Varlıklar',
    'resources' => 'Kaynaklar',
    'widgets' => 'Widget\'lar',
    'pages' => 'Sayfalar',
    'custom' => 'Özel İzinler',

    /**
     * HasPageShield Trait Forbidden Message
     */
    'forbidden' => 'Erişim izniniz yok',
];
