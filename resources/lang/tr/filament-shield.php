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

    /**
     * Role Setting Page
     */
    'page' => [
        'name' => 'Ayarlar',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Kaydet',
        'generate' => 'Kaydet & Oluştur'
    ],
    'labels.super_admin.toggle_input' => 'Süper Yönetici Rolü',
    'labels.super_admin.text_input' => 'Rol Adı',
    'labels.filament_user.toggle_input' => 'Filament Kullanıcı Rolü',
    'labels.filament_user.text_input' => 'Rol Adı',
    'labels.role_policy.toggle_input' => 'Rol Politikası Kayıtlı mı?',
    'labels.role_policy.message' => 'Politikanın kayıtlı olduğundan ve izinlerin uygulandığından emin olun',
    'labels.prefixes.placeholder' => 'Varsayılan İzin Önekleri',
    'labels.prefixes.resource' => 'Kaynak',
    'labels.prefixes.resource.placeholder' => 'Kaynak İzinlerini Ekle veya Kaldır...',
    'labels.prefixes.page' => 'Sayfa',
    'labels.prefixes.widget' => 'Araç',
    'labels.entities.placeholder' => 'Varlık İzin Oluşturucuları & Sekmeler',
    'labels.entities.message' => 'Oluşturucu & Sekmeler ',
    'labels.entities.resources' => 'Kaynaklar',
    'labels.entities.pages' => 'Sayfalar',
    'labels.entities.widgets' => 'Araçlar',
    'labels.entities.custom_permissions' => 'Özel İzinler',
    'labels.entities.custom_permissions.message' => 'Sekme',
    'labels.status.enabled' => 'Etkin',
    'labels.status.disabled' => 'Devre dışı',
    'labels.status.yes' => 'Evet',
    'labels.status.no' => 'Hayır',
    'labels.exclude.placeholder' => 'Dışlama Modu',
    'labels.exclude.message' => 'Dışlama Modunu Etkinleştirerek, izin oluşturucuya seçtiğiniz varlıklar için izin oluşturmayı atlaması talimatını verebilirsiniz.',
    'labels.exclude.resources' => 'Kaynaklar',
    'labels.exclude.resources.placeholder' => 'Kaynakları seçin ...',
    'labels.exclude.pages' => 'Sayfalar',
    'labels.exclude.pages.placeholder' => 'Sayfaları seçin ...',
    'labels.exclude.widgets' => 'Araçlar',
    'labels.exclude.widgets.placeholder' => 'Araçları seçin ...',

    /**
     * Messages
     */
    'forbidden' => 'Erişim izniniz yok',
    'update' => 'Kalkan yapılandırması yenilendi!',
    'generate' => 'Kalkan yapılandırması yenilendi & Politikalar olmadan izinler oluşturuldu!',
];
