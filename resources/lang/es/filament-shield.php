<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */
    
    'column.name' => 'Nombre',
    'column.guard_name' => 'Guard',
    'column.roles' => 'Roles',
    'column.permissions' => 'Permisos',
    'column.updated_at' => 'Actualizado el',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nombre',
    'field.guard_name' => 'Guard',
    'field.permissions' => 'Permisos',
    'field.select_all.name' => 'Seleccionar todos',
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

    /**
     * Role Setting Page
     */
    'page' => [
        'name' => 'Configuración',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Guardar',
        'generate' => 'Guardar y generar'
    ],
    'labels.super_admin.toggle_input' => 'Rol de súper administrador',
    'labels.super_admin.text_input' => 'Nombre del rol',
    'labels.filament_user.toggle_input' => 'Rol de usuario Filament',
    'labels.filament_user.text_input' => 'Nombre del rol',
    'labels.role_policy.toggle_input' => '¿Política del rol registrada?',
    'labels.role_policy.message' => 'Asegúrese que la política esté registrada y los permisos se apliquen',
    'labels.prefixes.placeholder' => 'Prefijos de permiso por defecto',
    'labels.prefixes.resource' => 'Recurso',
    'labels.prefixes.resource.placeholder' => 'Agregar o eliminar permisos del recurso...',
    'labels.prefixes.page' => 'Página',
    'labels.prefixes.widget' => 'Widget',
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
    'labels.exclude.placeholder' => 'Modo exclusión',
    'labels.exclude.message' => 'La habilitación del Modo Exclusión le permite instruir al generador del permiso omitir la creación de permisos para las entidades que seleccione.',
    'labels.exclude.resources' => 'Recursos',
    'labels.exclude.resources.placeholder' => 'Selecionar recursos ...',
    'labels.exclude.pages' => 'Páginas',
    'labels.exclude.pages.placeholder' => 'Seleccionar páginas ...',
    'labels.exclude.widgets' => 'Widgets',
    'labels.exclude.widgets.placeholder' => 'Seleccionar widgets ...',

    /**
     * Messages
     */
    'forbidden' => 'Usted no tiene permiso de acceso',
    'update' => '¡La configuración del Shield ha sido renovada!',
    'generate' => '¡La configuración del Shield ha sido renovada y se han generado los permisos y/o políticas!',

];

