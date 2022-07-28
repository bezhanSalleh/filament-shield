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
    'field.select_all.name' => 'Tümünü Seç',
    'field.select_all.message' => 'Bu rol için şu anda <span class="text-primary font-medium">Etkin</span> olan tüm İzinleri etkinleştirin',

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
    'widgets' => 'Araçlar',
    'pages' => 'Sayfalar',
    'custom' => 'Özel İzinler',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Erişim izniniz yok',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    // 'resource_permission_prefixes_labels' => [
    //     'view' => 'View',
    //     'view_any' => 'View Any',
    //     'create' => 'Create',
    //     'update' => 'Update',
    //     'delete' => 'Delete',
    //     'delete_any' => 'Delete Any',
    //     'force_delete' => 'Force Delete',
    //     'force_delete_any' => 'Force Delete Any',
    //     'restore' => 'Restore',
    //     'reorder' => 'Reorder',
    //     'restore_any' => 'Restore Any',
    //     'replicate' => 'Replicate',
    // ],
];
