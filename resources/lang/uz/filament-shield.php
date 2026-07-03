<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nomi',
    'column.guard_name' => 'Guard nomi',
    'column.roles' => 'Rollar',
    'column.permissions' => 'Ruxsatlar',
    'column.updated_at' => 'Yangilangan',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nomi',
    'field.guard_name' => 'Guard nomi',
    'field.permissions' => 'Ruxsatlar',
    'field.select_all.name' => 'Barchasini tanlash',
    'field.select_all.message' => 'Ushbu rol uchun <span class="text-primary font-medium">mavjud</span> bo‘lgan barcha ruxsatlarni yoqish',

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

    'section' => 'Obyektlar',
    'resources' => 'Resurslar',
    'widgets' => 'Vidjetlar',
    'pages' => 'Sahifalar',
    'custom' => 'Maxsus ruxsatlar',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Sizda kirish huquqi yo‘q',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Ko‘rish',
        'view_any' => 'Barchasini ko‘rish',
        'create' => 'Yaratish',
        'update' => 'Yangilash',
        'delete' => 'O‘chirish',
        'delete_any' => 'Barchasini o‘chirish',
        'force_delete' => 'Butunlay o‘chirish',
        'force_delete_any' => 'Barchasini butunlay o‘chirish',
        'restore' => 'Tiklash',
        'reorder' => 'Tartibini o‘zgartirish',
        'restore_any' => 'Barchasini tiklash',
        'replicate' => 'Nusxalash',
    ],
];
