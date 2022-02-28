<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nama',
    'column.guard_name' => 'Nama Penjaga',
    'column.roles' => 'Peran',
    'column.permissions' => 'Izin',
    'column.updated_at' => 'Dirubah',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nama',
    'field.guard_name' => 'Nama Penjaga',
    'field.permissions' => 'Izin',
    'field.select_all.name' => 'Pilih Semua',
    'field.select_all.message' => 'Aktifkan semua izin yang <span class="text-primary font-medium">Tersedia</span> untuk Peran ini.',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Pelindung',
    'nav.role.label' => 'Peran',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Peran',
    'resource.label.roles' => 'Peran',

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

    /**
     * Role Setting Page
     */
    'page' => [
        'name' => 'Setting',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Simpan',
        'generate' => 'Simpan & Generate'
    ],
    'labels.super_admin.toggle_input' => 'Peran Super Admin',
    'labels.super_admin.text_input' => 'Nama Peran',
    'labels.filament_user.toggle_input' => 'Peran Pengguna',
    'labels.filament_user.text_input' => 'Nama Peran',
    'labels.role_policy.toggle_input' => 'Policy Peran Terdaftar?',
    'labels.role_policy.message' => 'Pastikan policy terdaftar dan izin dipaksakan',
    'labels.prefixes.placeholder' => 'Awalan Izin Bawaan',
    'labels.prefixes.resource' => 'Sumber Daya',
    'labels.prefixes.resource.placeholder' => 'Tambah atau Hapus Izin Sumber Daya ...',
    'labels.prefixes.page' => 'Page',
    'labels.prefixes.widget' => 'Widget',
    'labels.entities.placeholder' => 'Generator Izin Entitas & Tab',
    'labels.entities.message' => 'Generator & Tab adalah ',
    'labels.entities.resources' => 'Sumber Daya',
    'labels.entities.pages' => 'Halaman',
    'labels.entities.widgets' => 'Widget',
    'labels.entities.custom_permissions' => 'Kustom Izin',
    'labels.entities.custom_permissions.message' => 'Tab adalah ',
    'labels.status.enabled' => 'Aktifkan',
    'labels.status.disabled' => 'Non Aktifkan',
    'labels.status.yes' => 'Ya',
    'labels.status.no' => 'Tidak',
    'labels.exclude.placeholder' => 'Mode Pengecualian',
    'labels.exclude.message' => 'Dengan mengaktifkan mode pengecualian, generator izin batal membuat izin untuk entitas yang kamu pilih.',
    'labels.exclude.resources' => 'Sumber Daya',
    'labels.exclude.resources.placeholder' => 'Pilih sumber daya ...',
    'labels.exclude.pages' => 'Halaman',
    'labels.exclude.pages.placeholder' => 'Pilih halaman ...',
    'labels.exclude.widgets' => 'Widget',
    'labels.exclude.widgets.placeholder' => 'Pilih widget ...',

    /**
     * Messages
     */
    'forbidden' => 'Kamu tidak punya izin akses',
    'update' => 'Pengaturan Pelindung Sudah Diperbarui!',
    'generate' => 'Pengaturan Pelindung Sudah Diperbarui & Izin Telat Dibuat Tanpa Policy!',
];
