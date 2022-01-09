<a href="https://github.com/bezhansalleh/filament-shield">
<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://user-images.githubusercontent.com/10007504/148662315-35d4bd74-fc1c-4f8c-8c02-689309b414b0.png" >
</a>

# Filament Shield

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bezhansalleh/filament-shield.svg?style=flat-square)](https://packagist.org/packages/bezhansalleh/filament-shield)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/run-tests?label=tests)](https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bezhansalleh/filament-shield.svg?style=flat-square)](https://packagist.org/packages/bezhansalleh/filament-shield)
#### The easiest and most intuitive way to add access management to your Filament Admin:
- :fire: **Resources** 📑
- :fire: **Pages** 📄
- :fire: **Widgets** 📈
  
One Plugin to rule them all, One Plugin to find them, One Plugin to bring them all, and in the light bind them, In the Land of Filament where building them is really fun!

## Support Filament

<a href="https://github.com/sponsors/danharrin">
<img width="320" alt="filament-logo" src="https://filamentadmin.com/images/sponsor-banner.jpg">
</a>

## Upgrade
To upgrade to the latest release first run:
```bash
composer update
```
then:
```bash
php artisan shield:upgrade
```
you can run this command without interaction by supplying the `-no-interaction` flag.
## Installation (New Apps)

1. Install the package via composer:

```bash
composer require bezhansalleh/filament-shield
```

2. Publish the config file with:

```bash
php artisan vendor:publish --tag="filament-shield-config"
```

3. Configure your options
```php
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
        'enabled' => false,
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

    /**
     * Register `RolePolicy` for `RoleResource`
     */
    'register_role_policy' => true,
];
```
4. Add the `Spatie\Permission\Traits\HasRoles` trait to your User model(s):

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ...
}
```

5. Now run the following command to setup everything:

```bash
php artisan shield:install
```
Follow the prompts and enjoy!

## Installation (Existing Apps)
##### Apps with `spatie/laravel-permission` already installed
5. First remove the `filament-shield` config file and translations then republish them by running the following commands:
```bash
php artisan vendor:publish --tag="filament-shield-config"
php artisan vendor:publish --tag="filament-shield-translations"
php artisan config:clear
```
Follow the above steps from (1) thorugh (4) run the following command to setup everything:

```bash
php artisan shield:install --fresh
```

Beaware, that even though there are checks in place but if confirmed; existing policies might get overwritten.

### Default Behaviour

By default running `php artisan shield:install` will exclude generating permissions for `FilamentAdmin`'s default `Dashboard` page, `AccountWidget` and `FilamentInfoWidget`.
If you want to disable this behaviour you can supply the `--all` flag:
```bash
php artisan shield:install --all
```
or
```bash
php artisan shield:install --fresh --all
```
Or you could remove them from the respective `except` key in the config file.

#### Resource Custom Permissions
You can add custom permissions for the resources in addition to the required 6 by adding your custom permission names at the end of the `resource_permissions_prefixes` config key array.
For instance lets add `export` cutom permission to all resources:
```php
...
'resource_permission_prefixes' => [
        'view',
        'view_any',
        'create',
        'delete',
        'delete_any',
        'update',
        'export',
    ],
...
```
Since we have added our new custom permission, it's time to refresh the list of permissions for the resources by running:
```bash
php artisan shield:generate --except
```
Now, you can check and see in your `Resources` each resource listed will have an `export` permission as well.
#### Pages
If you have generated permissions for `Pages` you can toggle the page's navigation from sidebar and restricted access to the page. You can set this up manually but this package comes with a `HasPageShield` trait to speed up this process. All you have to do is use the trait in you pages:
```php
<?php

namespace App\Filament\Pages;

use ...;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class MyPage extends Page
{
    use HasPageShield;
    ...
}
```

📕 <b style="color:darkred">However if your page's `mount()` method requires a `$record` or other `parameters` it's best to handle that yourself instead of using `HasPageShield`.</b>
#### Widgets
if you have generated permissions for `Widgets` you can toggle their state based on whether a user have permission or not. You can set this up manually but this package comes with a `HasWidgetShield` trait to speed up this process. All you have to do is use the trait in you widgets:
```php
<?php

namespace App\Filament\Widgets;

use ...;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class IncomeWidget extends LineChartWidget
{
    use HasWidgetShield;
    ...
}
```
#### `RolePolicy` 
You can skip this if have set the `'register_role_policy' => true` in the config.
To ensure `RoleResource` access via `RolePolicy` you would need to add the following to your `AuthServiceProvider`:

```php
//AuthServiceProvider.php
...
protected $policies = [
    'Spatie\Permission\Models\Role' => 'App\Policies\RolePolicy',
];
...
```

Optionally, you can publish the translations using

```bash
php artisan vendor:publish --tag="filament-shield-translations"
```

### Available Filament Shield Commands

```bash
  shield:create    # Create Permissions and/or Policy for the given Filament Resource Model
  shield:generate  # (Re)Discovers Filament resources and (re)generates Permissions and Policies.
  shield:install   # One Command to Rule them All 🔥
  shield:publish   # Publish filament shield's Resource.
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bezhan Salleh](https://github.com/bezhanSalleh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
