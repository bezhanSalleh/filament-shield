<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Ad',
    'column.guard_name' => 'Guard Adı',
    'column.team' => 'Komanda',
    'column.roles' => 'Rollar',
    'column.permissions' => 'İcazələr',
    'column.updated_at' => 'Yenilənmə Tarixi',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Ad',
    'field.guard_name' => 'Guard Adı',
    'field.permissions' => 'İcazələr',
    'field.team' => 'Komanda',
    'field.team.placeholder' => 'Komanda seçin ...',
    'field.select_all.name' => 'Hamısını Seç',
    'field.select_all.message' => 'Bu rol üçün bütün icazələri aktivləşdirir/deaktivləşdirir',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rollar',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Rollar',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Obyektlər',
    'resources' => 'Resurslar',
    'widgets' => 'Vidcetlər',
    'pages' => 'Səhifələr',
    'custom' => 'Xüsusi İcazələr',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Bu bölməyə giriş icazəniz yoxdur',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Bax',
        'view_any' => 'Hamısına Bax',
        'create' => 'Yarat',
        'update' => 'Yenilə',
        'delete' => 'Sil',
        'delete_any' => 'Hər Hansınısa Sil',
        'force_delete' => 'Tam Sil',
        'force_delete_any' => 'Hər Hansınısa Tam Sil',
        'restore' => 'Bərpa Et',
        'reorder' => 'Sırala',
        'restore_any' => 'Hər Hansınısa Bərpa Et',
        'replicate' => 'Kopyala',
    ],
];