<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nombre',
    'column.guard_name' => 'Nombre Guard',
    'column.roles' => 'Roles',
    'column.permissions' => 'Permisos',
    'column.updated_at' => 'Actualizado el',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nombre',
    'field.guard_name' => 'Nombre Guard',
    'field.permissions' => 'Permisos',
    'field.select_all.name' => 'Seleccionar todo',
    'field.select_all.message' => 'Habilitar todos los permisos actualmente <span class="text-primary font-medium">habilitados</span> para este rol',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Roles',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Roles',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entidades',
    'resources' => 'Recursos',
    'widgets' => 'Widgets',
    'pages' => 'Páginas',
    'custom' => 'Permisos personalizados',

    /*
    |--------------------------------------------------------------------------
    | Shield Settings Page
    |--------------------------------------------------------------------------
    */

    'page' => [
        'name' => 'Ajustes Shield',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Guardar',
        'generate' => 'Guardar y generar',
        'load_default_settings' => 'Valores por defecto',
    ],

    'settings' => [
        'enabled' =>  false,
        'label' => 'Página Ajustes',
        'helper_text' => 'Habilitar/deshabilitar la página de Ajustes. Solo disponible para Super Admin.',
        'navigation_label' => 'Ajustes',

        'generator_options' => [
            'policies_and_permissions' => 'Generar Politicas y Permisos',
            'policies' => 'Generar solo Políticas',
            'permissions' => 'Generar solo Permisos',
        ],

        'auth_provider' => [
            'label' => 'Modelo Auth Provider',
            'helper_text' => 'Nombre de clase completo del modelo utilizado para la generación de directivas.'
        ],

        'resource' => [
            'name' => 'Recurso de Roles Shield',
            'slug' => 'Slug',
            'navigation_sort' => 'Ordenación de navegación',
        ]
    ],


    /*
    |--------------------------------------------------------------------------
    | MISC Labels
    |--------------------------------------------------------------------------
    */

    'labels.super_admin.toggle_input' => 'Rol de súper administrador',
    'labels.super_admin.text_input' => 'Nombre del rol',
    'labels.filament_user.toggle_input' => 'Rol de usuario Filament',
    'labels.filament_user.text_input' => 'Nombre del rol',
    'labels.role_policy.toggle_input' => '¿Política del rol registrada?',
    'labels.role_policy.message' => 'Asegúrese que la política esté registrada y los permisos se apliquen.',
    'labels.permission_prefixes.placeholder' => 'Prefijos de permiso por defecto',
    'labels.permission_prefixes.resource' => 'Recurso',
    'labels.permission_prefixes.resource.placeholder' => 'Agregar o eliminar permisos del recurso...',
    'labels.permission_prefixes.page' => 'Página',
    'labels.permission_prefixes.widget' => 'Widget',
    'labels.entities.placeholder' => 'Generadores de permisos de entidad y pestañas',
    'labels.entities.message' => 'Los generadores y pestañas son ',
    'labels.entities.resources' => 'Recursos',
    'labels.entities.pages' => 'Páginas',
    'labels.entities.widgets' => 'Widgets',
    'labels.entities.custom_permissions' => 'Permisos personalizados',
    'labels.entities.custom_permissions.message' => 'La pestaña es ',
    'labels.status.enabled' => 'Habilitado',
    'labels.status.disabled' => 'Deshabilitado',
    'labels.status.yes' => 'Sí',
    'labels.status.no' => 'No',
    'labels.exclude.placeholder' => 'Modo Exclusión',
    'labels.exclude.message' => 'La habilitación del Modo Exclusión le permite instruir al generador del permiso omitir la creación de permisos para las entidades que seleccione.',
    'labels.exclude.resources' => 'Recursos',
    'labels.exclude.resources.placeholder' => 'Selecionar recursos ...',
    'labels.exclude.pages' => 'Páginas',
    'labels.exclude.pages.placeholder' => 'Seleccionar páginas ...',
    'labels.exclude.widgets' => 'Widgets',
    'labels.exclude.widgets.placeholder' => 'Seleccionar widgets ...',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Usted no tiene permiso de acceso',
    'update' => '¡La configuración del Shield ha sido renovada!',
    'generate' => '¡La configuración del Shield ha sido renovada y se han generado los permisos y/o políticas!',
    'loaded_default_settings' => 'Ajustes predeterminados de Shield cargados.',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'View',
        'view_any' => 'View Any',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'delete_any' => 'Delete Any',
        'force_delete' => 'Force Delete',
        'force_delete_any' => 'Force Delete Any',
        'restore' => 'Restore',
        'restore_any' => 'Restore Any',
        'replicate' => 'Replicate',
    ],
];
