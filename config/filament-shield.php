<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Defualt Roles
    |--------------------------------------------------------------------------
    |
    | The default is `super_admin` but you can change it to whatever works best for you.
    | `filament_user` if enabled will help smoothly provide access to filament users
    | in production when implementing `FilamentUser`
    */

    'default_roles' => [
        'super_admin_role_name' => 'super_admin',
        'filament_user' => [
            'role_name' => 'filament_user',
            'enabled' => true
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Resource Permission Prefixes
    |--------------------------------------------------------------------------
    |
    | When generating permissions for a `Resource` the resource `Model` will be prefixed with these.
    | But you are free to change them in to whatever works for you, but keeping in mind the order since
    | these will also be used in generating policies for the resources.
    */

    'resource_permission_prefixes' => [
        'view',
        'view_any',
        'create',
        'delete',
        'delete_any',
        'update',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Widget and Page Permission Prefix
    |--------------------------------------------------------------------------
    |
    | When generating permission for a `Widget` or `Page` the widget or page name will be prefixed
    | with this. But you are free to change it in to whatever works for you.
    */

    'page_permission_prefix' => 'view',
    'widget_permission_prefix' => 'view',

    /*
    |--------------------------------------------------------------------------
    | Entities Permission Generator
    |--------------------------------------------------------------------------
    | Enable the Entities for which you want the permissions or permissions and policies
    | to be auto generated when you run `php artisan shield:install` command.
    */

    'entities' => [
        'pages' => false,
        'widgets' => true,
        'resources' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Show/Hide Entities
    |--------------------------------------------------------------------------
    | You can show and hide entities in the shield manager while creating and editing roles.
    */

    'tabs' => [
        'pages' => false,
        'widgets' => true,
        'resources' => true,
        'custom_permissions' => false,
    ],


    /*
    |--------------------------------------------------------------------------
    | Only
    |--------------------------------------------------------------------------
    | Generate only permissions or permissions with policies for the entities
    | listed here.
    */

    'only' => [

        /*----------------------------------------------------------------------*
        | Generate Permissions for Only these Pages.                            |
        | The generated permission for a Page:                                  |
        | `view_page_name` i.e, `SettingsPage` => `view_settings_page`          |
        *-----------------------------------------------------------------------*/
        'pages' => [],

        /*----------------------------------------------------------------------*
        | Generate Permissions for Only these Widgets.                          |
        | The generated permission for a Widget:                                |
        | `view_widget_name` i.e, `IncomeWidget` => `view_income_widget`        |
        *-----------------------------------------------------------------------*/
        'widgets' => [],

        /*----------------------------------------------------------------------*
        |  Generate Permissions and Policies for Only these resource `Models`   |
        |  You can pass in the list of `Models` i.e, ['User','Role',...]        |
        |  Or the `Resources` i.e, ['UserResource','RoleResource',...]          |
        *-----------------------------------------------------------------------*/
        'resources' => [],
    ],


    /*
    |--------------------------------------------------------------------------
    | Except
    |--------------------------------------------------------------------------
    | Generate permissions or permissions with policies for all the entities
    | except listed here.
    | Generated Permission name will be formatted as:
    | Page: `view_page_name` i.e, `SettingsPage` => `view_settings_page`
    | Widget: `view_widget_name` i.e, `IncomeWidget` => `view_income_widget`
    */

    'except' => [
        'pages' => [
            'Dashboard'
        ],
        'widgets' => [
            'AccountWidget',
            'FilamentInfoWidget'
        ],
        'resources' => [],
    ],

    'register_role_policy' => true,
];
