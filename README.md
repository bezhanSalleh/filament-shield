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
</p>

<hr style="background-color: #ebb304">

# Filament Shield
The easiest and most intuitive way to add access management to your Filament Admin:
- :fire: **Resources** 
- :fire: **Pages** 
- :fire: **Widgets** 
- :fire: **Settings**
<!-- One Plugin to rule them all, One Plugin to find them, One Plugin to bring them all, and in the light bind them, In the Land of Filament where building them is really fun! -->

## Upgrade 

To upgrade to the latest release first run:

```bash
composer update
```
#### v2.x
> **Note** 
> Minimum **Filament** Requirement is now `2.13`.

**Before following along, backup your current `config` first.**

1. Upgrade from **`1.x`**
   1. Delete `Shield` folder from `App\Filament\Resources`
   2. Delete `filament-shield` from `resources\lang\vendor`
   3. Delete `filament-shield.php` from `Config`
   4. Now either do **`2.Upgrade only`** or **`3.Upgrade and enable Setting Page`**

2. **Upgrade only**
   1. Publish the `Config`:
        ```bash
        php artisan vendor:publish --tag=filament-shield-config --force
        ```
   2. Configure:
        Update the new **`published config`** based on your **`backed-up config`**

   3. Install:
        ```bash
        php artisan shield:install --fresh
        ```

   4. Generate:
        ```bash
        php artisan shield:generate
        ```

3. **Upgrade** and enable **`Setting`** Page
    1. Follow **Upgrade only**'s step 1 & 2
   
    2. Run the following command
        ```bash
        php artisan shield:install --fresh --setting
        ```
   
> **Note**
> for **Filament** prior to 2.13 use [v1.1.12](https://github.com/bezhanSalleh/filament-shield/releases/tag/v1.1.12)
## Installation

1. Install the package via composer:

```bash
composer require bezhansalleh/filament-shield
```

2. Add the `Spatie\Permission\Traits\HasRoles` trait to your User model(s):

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles; //or HasFilamentShield

    // ...
}
```
3. Publish the `config` using:
```bash
php artisan vendor:publish --tag=filament-shield-config
```
4. Setup your configuration
```php
<?php

    return [

          'shield_resource' => [
              'slug' => 'shield/roles',
              'navigation_sort' => -1,
          ],

          'auth_provider_model' => [
              'fqcn' => 'App\\Models\\User'
          ],

          'settings' => [
              'enabled' => false,
          ],

          'super_admin' => [
              'enabled' => true,
              'name'  => 'super_admin'
          ],

          'filament_user' => [
              'enabled' => false,
              'name' => 'filament_user'
          ],

          'permission_prefixes' => [
              'resource' => [
                  'view',
                  'view_any',
                  'create',
                  'update',
                  'restore',
                  'restore_any',
                  'replicate',
                  'delete',
                  'delete_any',
                  'force_delete',
                  'force_delete_any',
              ],

              'page' => 'page',
              'widget' => 'widget',
          ],

          'entities' => [
              'pages' => true,
              'widgets' => true,
              'resources' => true,
              'custom_permissions' => false,
          ],

          'generator' => [
              'option' => 'policies_and_permissions'
          ],

          'exclude' => [
              'enabled' => true,

              'pages' => [
                  'Dashboard',
              ],

              'widgets' => [
                  'AccountWidget','FilamentInfoWidget',
              ],

              'resources' => [],
          ],

          'register_role_policy' => [
              'enabled' => false
          ],
    ];
```
4. Now run the following command to install shield:
    1. Install only 
         ```bash
         php artisan shield:install
         ```
    2. Install and Enable `Setting` Page
        ```bash
         php artisan shield:install --setting
         ```

Follow the prompts and enjoy!

#### Resource Custom Permissions

You can add custom permissions for `Resources` through settings page.

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

📕 <b style="color:darkred">`HasPageShield` uses the `booted` method to check the user's permissions and makes sure to execute the `booted` page method in the parent page if exists.</b>

###### Pages Hooks

However if you need to perform some methods before and after the booted method you can declare the next hooks methods in your filament page.

```php
<?php

namespace App\Filament\Pages;

use ...;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class MyPage extends Page
{
    use HasPageShield;
    ...

    protected function beforeBooted : void() {
        ...
    }

    protected function afterBooted : void() {
        ...
    }

    /**
     * Hook to perform an action before redirect if the user
     * doesn't have access to the page.  
     * */
    protected function beforeShieldRedirects : void() {
        ...
    }
}
```

###### Pages Redirect Path

`HasPageShield` uses the `config('filament.path')` value by default to perform the shield redirection. If you need to overwrite the rediretion path, just add the next method to your page:

```php
<?php

namespace App\Filament\Pages;

use ...;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class MyPage extends Page
{
    use HasPageShield;
    ...

    protected function getShieldRedirectPath(): string {
        return '/'; // redirect to the root index...
    }
}
```

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

## Available Filament Shield Commands

```php
- install   # One Command to Rule them All 🔥
- generate  # (Re)Discovers Filament resources and (re)generates Permissions and Policies.
- create    # Create Permissions and/or Policy for the given Filament Resource Model
- super-admin # Create a user with super_admin role
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

If you want to contribute to this packages, you may want to test it in a real Filament project:

- Fork this repository to your GitHub account.
- Create a Filament app locally.
- Clone your fork in your Filament app's root directory.
- In the `/filament-shield` directory, create a branch for your fix, e.g. `fix/error-message`.

Install the packages in your app's `composer.json`:

```json
"require": {
    "bezhansalleh/filament-shield": "dev-fix/error-message as main-dev",
},
"repositories": [
    {
        "type": "path",
        "url": "filament-shield"
    }
]
```

Now, run `composer update`.


Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bezhan Salleh](https://github.com/bezhanSalleh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
