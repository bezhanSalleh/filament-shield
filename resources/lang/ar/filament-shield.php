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
    'custom' => 'أذونات مخصصة',

    /**
     * Role Setting Page
     */
    'page' => [
        'name' => 'إعدادات',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'حفظ',
        'generate' => 'حفظ وإنشاء',
    ],
    'labels.super_admin.toggle_input' => 'صلاحية المدير العام',
    'labels.super_admin.text_input' => 'اسم الصلاحية',
    'labels.filament_user.toggle_input' => 'صلاحية المستخدم',
    'labels.filament_user.text_input' => 'اسم الصلاحية',
    'labels.role_policy.toggle_input' => 'تسجيل سياسة الصلاحية؟',
    'labels.role_policy.message' => 'تأكد من تسجيل السياسات وفرض الأذونات',
    'labels.prefixes.placeholder' => 'البادئة الافتراضية للأذونات',
    'labels.prefixes.resource' => 'المصدر',
    'labels.prefixes.resource.placeholder' => 'إضافة أو إزالة مصادر الأذونات...',
    'labels.prefixes.page' => 'صفحة',
    'labels.prefixes.widget' => 'ملحق',
    'labels.entities.placeholder' => 'أذونات المولّدات وعلامات التبويب',
    'labels.entities.message' => 'مولّدات وعلامات التبويب ',
    'labels.entities.resources' => 'المصادر',
    'labels.entities.pages' => 'الصفحات',
    'labels.entities.widgets' => 'الملحقات',
    'labels.entities.custom_permissions' => 'أذونات مخصصة',
    'labels.entities.custom_permissions.message' => 'علامة التبويب ',
    'labels.status.enabled' => 'مفعل',
    'labels.status.disabled' => 'غير مفعل',
    'labels.status.yes' => 'نعم',
    'labels.status.no' => 'لا',
    'labels.exclude.placeholder' => 'وضع الاستبعاد',
    'labels.exclude.message' => 'من خلال تمكين وضع الاستبعاد، يمكنك توجيه مولّد الأذونات لتخطي إنشاء الأذونات للكيانات التي تحددها',
    'labels.exclude.resources' => 'المصادر',
    'labels.exclude.resources.placeholder' => 'اختر المصادر ...',
    'labels.exclude.pages' => 'الصفحات',
    'labels.exclude.pages.placeholder' => 'اختر الصفحات ...',
    'labels.exclude.widgets' => 'الملاحق',
    'labels.exclude.widgets.placeholder' => 'اختر الملاحق ...',

    /**
     * Messages
     */
    'forbidden' => 'عذراً، ليست لديك صلاحية تنفيذ هذا الإجراء',
    'update' => 'تم تحديث إعدادات Shield!',
    'generate' => 'تم تحديث إعدادات Shield وإنشاء الأذونات مع/بدون السياسات!',
];
