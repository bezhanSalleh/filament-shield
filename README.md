<a href="https://github.com/bezhansalleh/filament-shield">
<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://user-images.githubusercontent.com/10007504/148662315-35d4bd74-fc1c-4f8c-8c02-689309b414b0.png" >
</a>

<p align="center">
    <a href="https://filamentadmin.com/docs/2.x/admin/installation">
        <img alt="FILAMENT 8.x" src="https://img.shields.io/badge/FILAMENT-2.x-EBB304?style=for-the-badge">
    </a>
    <a href="https://packagist.org/packages/bezhansalleh/filament-shield">
        <img alt="Packagist" src="https://img.shields.io/packagist/v/bezhansalleh/filament-shield.svg?style=for-the-badge&logo=packagist">
    </a>
    <a href="https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3Arun-tests+branch%3Amain">
        <img alt="Tests Passing" src="https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/run-tests?style=for-the-badge&logo=github&label=tests">
    </a>
    <a href="https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain">
        <img alt="Code Style Passing" src="https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/run-tests?style=for-the-badge&logo=github&label=code%20style">
    </a>

<a href="https://packagist.org/packages/bezhansalleh/filament-shield">
    <img alt="Downloads" src="https://img.shields.io/packagist/dt/bezhansalleh/filament-shield.svg?style=for-the-badge" >
    </a>
<p>
<hr>
# Filament Shield
The easiest and most intuitive way to add access management to your Filament Admin:
- :fire: **Resources** 
- :fire: **Pages** 
- :fire: **Widgets** 
- :fire: **Settings** <sup><i style="color:red;">New</i></sup>
<!-- One Plugin to rule them all, One Plugin to find them, One Plugin to bring them all, and in the light bind them, In the Land of Filament where building them is really fun! -->

## Support Filament

<a href="https://github.com/sponsors/danharrin">
<img width="320" alt="filament-logo" src="https://filamentadmin.com/images/sponsor-banner.jpg">
</a>

## Upgrade
To upgrade to the latest release first run:
```bash
composer update
```
backup `config.php`(incase you have configured) then:
```bash
php artisan shield:upgrade
```
you can run this command without interaction by supplying the `-no-interaction` flag.

<hr style="background-color: #ebb304">
## Installation

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
    | Permissions' generated will be assigned automatically to the following roles when enabled.
    | `filament_user` if enabled will help smoothly provide access to filament users
    | in production when implementing `FilamentUser` interface.
    */


    'super_admin' => [
        'enabled' => true,
        'role_name' => 'super_admin'
    ],

    'filament_user' => [
        'role_name' => 'filament_user',
        'enabled' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Prefixes
    |--------------------------------------------------------------------------
    |
    | When generating permissions for a `Resource` the resource `Model` will be prefixed with these.
    | Keep in mind the order since these will also be used in generating policies for the resources.
    |
    | When generating permission for a `Widget` or `Page` the widget or page name will be prefixed
    | with this.
    | But you are free to change these in to whatever works for you.
    */

    'prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'delete',
            'delete_any',
            'update',
            'export', // custom resource permission
        ],
        'page'  =>  'view',
        'widget' => 'view'
    ],

    /*
    |--------------------------------------------------------------------------
    | Entities Permission Generator
    |--------------------------------------------------------------------------
    | Enable the Entities for which you want the permissions or permissions and policies
    | to be auto generated when you run `php artisan shield:install` command.
    */

    'entities' => [
        'pages' => true,
        'widgets' => true,
        'resources' => true,
        'custom_permissions' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude
    |--------------------------------------------------------------------------
    | When enabled Exclude entites listed here during permission generation.
    |
    */

    'exclude' => [
        'enabled' => true,
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
<hr style="background-color: #ebb304">

#### Resource Custom Permissions
You can add custom permissions for the resources in addition to the required 6 by adding your custom permission names at the end of the `resource_permissions_prefixes` config key array.
For instance lets add `export` cutom permission to all resources:
```php
...
'prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'delete',
            'delete_any',
            'update',
            'export', // custom resource permission
        ],
    ...
...
```
Since we have added our new custom permission, it's time to refresh the list of permissions for the resources by running:
```bash
php artisan shield:generate
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

#### Translations 
Publish the translations using:

```bash
php artisan vendor:publish --tag="filament-shield-translations"
```

#### Views 
Publish the Views using:

```bash
php artisan vendor:publish --tag="filament-shield-views"
```
<hr style="background-color: #ebb304">

### Available Filament Shield Commands

```php
- install   # One Command to Rule them All 🔥
- generate  # (Re)Discovers Filament resources and (re)generates Permissions and Policies.
- create    # Create Permissions and/or Policy for the given Filament Resource Model
- publish   # Publish filament shield's Resource.
- super-admin # Create a user with super_admin role
- upgrade # upgrade shield without hassle
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
