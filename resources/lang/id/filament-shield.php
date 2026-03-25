<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nama',
    'column.guard_name' => 'Nama Guard',
    'column.roles' => 'Peran',
    'column.permissions' => 'Izin',
    'column.updated_at' => 'Terakhir Diubah',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nama',
    'field.guard_name' => 'Nama Guard',
    'field.permissions' => 'Izin',
    'field.select_all.name' => 'Pilih Semua',
    'field.select_all.message' => 'Aktifkan semua izin yang <span class="text-primary font-medium">tersedia</span> untuk peran ini.',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Peran',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Peran',
    'resource.label.roles' => 'Daftar Peran',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entitas',
    'resources' => 'Sumber Daya',
    'widgets' => 'Widget',
    'pages' => 'Halaman',
    'custom' => 'Izin Kustom',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Anda tidak memiliki izin untuk mengakses halaman ini.',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Lihat',
        'view_any' => 'Lihat semua',
        'create' => 'Buat',
        'update' => 'Ubah',
        'delete' => 'Hapus',
        'delete_any' => 'Hapus semua',
        'force_delete' => 'Hapus permanen',
        'force_delete_any' => 'Hapus permanen semua',
        'restore' => 'Pulihkan',
        'replicate' => 'Duplikat',
        'reorder' => 'Urutkan ulang',
        'restore_any' => 'Pulihkan semua',
    ],
];
