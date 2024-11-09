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

    'resource_permission_prefixes_labels' => [
        'view' => 'Görüntüle',
        'view_any' => 'Herhangi birini görüntüle',
        'create' => 'Oluştur',
        'update' => 'Güncelle',
        'delete' => 'Sil',
        'delete_any' => 'Herhangi Birini Sil',
        'force_delete' => 'Kalıcı Sil',
        'force_delete_any' => 'Herhangi Birini Kalıcı Sil',
        'restore' => 'Geri Yükle',
        'reorder' => 'Sırala',
        'restore_any' => 'Herhangi Birini Geri Yükle',
        'replicate' => 'Kopyala/Çoğalt',
    ],
];
