<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'الإسم',
    'column.guard_name' => 'اسم الحارس',
    'column.roles' => 'الصلاحية',
    'column.permissions' => 'الأذونات',
    'column.updated_at' => 'تاريخ التحديث',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'الإسم',
    'field.guard_name' => 'اسم الحارس',
    'field.permissions' => 'الأذونات',
    'field.select_all.name' => 'تحديد الكل',
    'field.select_all.message' => 'تفعيل كافة الأذونات لهذه الصلاحية',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'الصلاحيات',
    'nav.role.label' => 'الصلاحيات',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'صلاحية',
    'resource.label.roles' => 'الصلاحيات',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'الوحدات',
    'resources' => 'المصادر',
    'widgets' => 'الملحقات',
    'pages' => 'الصفحات',
    'panels'  => 'لوحات',
    'custom' => 'أذونات مخصصة',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'ليس لديك الإذن للوصول',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'عرض',
        'view_any' => 'عرض الكل',
        'create' => 'إضافة',
        'update' => 'تعديل',
        'delete' => 'حذف',
        'delete_any' => 'حذف الكل',
        'force_delete' => 'فرض الحذف',
        'force_delete_any' => 'فرض حذف أي',
        'reorder' => 'إعادة ترتيب',
        'restore' => 'استرجاع',
        'restore_any' => 'استرجاع الكل',
        'replicate' => 'استنساخ',
    ],
];
