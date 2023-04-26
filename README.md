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
        <img alt="Tests Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-shield/run-tests.yml?style=for-the-badge&logo=github&label=tests">
    </a>
    <a href="https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain">
        <img alt="Code Style Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-shield/laravel-pint.yml?style=for-the-badge&logo=github&label=code%20style">
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
- :fire: **Custom Permissions**
<!-- One Plugin to rule them all, One Plugin to find them, One Plugin to bring them all, and in the light bind them, In the Land of Filament where building them is really fun! -->


Table of contents
=================

- [Filament Shield](#filament-shield)
- [Table of contents](#table-of-contents)
  - [Upgrade](#upgrade)
      - [v2.x](#v2x)
  - [Installation](#installation)
      - [Resources](#resources)
        - [Default](#default)
        - [Custom Permissions](#custom-permissions)
        - [Configure Permission Identifier](#configure-permission-identifier)
        - [Custom Navigation Group](#custom-navigation-group)
      - [Pages](#pages)
          - [Pages Hooks](#pages-hooks)
          - [Pages Redirect Path](#pages-redirect-path)
      - [Widgets](#widgets)
      - [Role Policy](#role-policy)
      - [Third-Party Plugins](#third-party-plugins)
      - [Translations](#translations)
  - [Available Filament Shield Commands](#available-filament-shield-commands)
      - [`shield:doctor`](#shielddoctor)
      - [`shield:install`](#shieldinstall)
      - [`shield:generate`](#shieldgenerate)
      - [`shield:super-admin`](#shieldsuper-admin)
    - [`shield:publish`](#shieldpublish)
    - [`shield:seeder`](#shieldseeder)
      - [`shield:upgrade`](#shieldupgrade)
  - [Testing](#testing)
  - [Changelog](#changelog)
  - [Contributing](#contributing)
  - [Security Vulnerabilities](#security-vulnerabilities)
  - [Credits](#credits)
  - [License](#license)


<hr style="background-color: #ebb304">


## Upgrade 

To upgrade to the latest release first run:

```bash
composer update
```
#### v2.x
> **Note** 
> Minimum **Filament** Requirement is now `2.13`. for older versions use [v1.1.12](https://github.com/bezhanSalleh/filament-shield/releases/tag/v1.1.12)

**Before following along, backup your current `config` first.**

1. Upgrade from **`1.x`**
   1. Delete `Shield` folder from `App\Filament\Resources`
   2. Delete `filament-shield` from `resources\lang\vendor or lang\vendor`
   3. Delete `filament-shield` from `resources\views\vendor`
   4. Delete `filament-shield.php` from `Config`
   5. Now follow **`Upgrade only`**

2. Upgrade only **`2.x`**
   1. Publish the `Config`:
        ```bash
        php artisan vendor:publish --tag=filament-shield-config --force
        ```
   2. Configure:
        Update the new **`published config`** based on your **`backed-up config`**

   3. Install:
        ```bash
        php artisan shield:upgrade
        ```

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
              'navigation_badge' => true,
              'navigation_group' => true,
              'is_globally_searchable' => false,
              'show_model_path' => true,
          ],

          'auth_provider_model' => [
              'fqcn' => 'App\\Models\\User'
          ],

          'super_admin' => [
              'enabled' => true,
              'name'  => 'super_admin',
              'define_via_gate' => false,
              'intercept_gate' => 'before' // after
          ],

          'filament_user' => [
              'enabled' => true,
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
                  'reorder',
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
              'enabled' => true
          ],
    ];
```
4. Now run the following command to install shield:
```bash
php artisan shield:install
```

Follow the prompts and enjoy!

#### Resources
Generally there are two scenarios that shield handles permissions for your `Filament` resources.

##### Default
Out of the box `Shield` handles the predefined permissions for `Filament` resources. So if that's all that you need you are all set. 
If you need to add a single permission (for instance `lock`) and have it available for all your resources just append it to the following `config` key:

```php
    permission_prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'lock'
        ],
        ...
    ],
```
:bulb: Now you are thinking **`what if I need a permission to be only available for just one resource?`**
No worries, that's where [Custom Permissions](#custom-permissions) come to play.

##### Custom Permissions
To define custom permissions per `Resource` your `Resource` must implement the `HasShieldPermissions` contract.
This contract has a `getPermissionPrefixes()` method which returns an array of permission prefixes for your `Resource`.

Consider you have a `PostResource` and you want a couple of the predefined permissions plus a new permission called `publish_posts` to be only available for `PostResource` only.

```php
<?php

namespace BezhanSalleh\FilamentShield\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
...

class PostResource extends Resource implements HasShieldPermissions
{
    ...

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish'
        ];
    }

    ...
}
```
In the above example the `getPermissionPrefixes()` method returns the permission prefixes `Shield` needs to generate the permissions.

✅ Now to enforce `publish_post` permission headover to your `PostPolicy` and add a `publish()` method:
```php
    /**
     * Determine whether the user can publish posts.
     *
     * @param  \App\Models\User  $admin
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function publish(User $user)
    {
        return $user->can('publish_post');
    } 
```
🅰️/🈯️ To make the prefix translatable, publish `Shield`'s translations and append the prefix inside `resource_permission_prefixes_labels` as key and it's translation as value for the languages you need.
```php
//lang/en/filament-shield.php
'resource_permission_prefixes_labels' => [
    'publish' => 'Publish'    
],
//lang/es/filament-shield.php
'resource_permission_prefixes_labels' => [
    'publish' => 'Publicar'    
],
```

##### Configure Permission Identifier
By default the permission identifier is generated as follow:
```php
Str::of($resource)
    ->afterLast('Resources\\')
    ->before('Resource')
    ->replace('\\', '')
    ->snake()
    ->replace('_', '::');
```
So for instance if you have a resource like `App\Filament\Resources\Shop\CategoryResource` then the permission identifier would be `shop::category` and then it would be prefixed with your defined prefixes or what comes default with shield.

If you wish to change the default behaviour, then you can call the static `configurePermissionIdentifierUsing()` method inside a service provider's `boot()` method, to which you pass a Closure to modify the logic. The Closure receives the fully qualified class name of the resource as `$resource` which gives you the ability to access any property or method defined within the resource.

For example, if you wish to use the model name as the permission identifier, you can do it like so:

```php
use BezhanSalleh\FilamentShield\Facades\FilamentShield;

FilamentShield::configurePermissionIdentifierUsing(
    fn($resource) => str($resource::getModel())
        ->afterLast('\\')
        ->lower()
        ->toString()
);
```
> **Warning**
> Keep in mind that ensuring the uniqueness of the permission identifier is now up to you.

##### Custom Navigation Group
By default the english translation renders Roles and Permissions under 'Filament Shield' if you wish to change this, first publish the [translations files](#translations) and change the relative locale to the group of your choosing for example:

```php
'nav.group' => 'Filament Shield',
```

to

```php
'nav.group' => 'User Management',
```
apply this to each lanugage you have groups in.

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

#### Role Policy

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

#### Third-Party Plugins

Shield also generates policies and permissions for third-party plugins and to enforce the generated policies you will need to register them in your application's `AuthServiceProvider`:
```
...
class AuthServiceProvider extends ServiceProvider
{
    ...
    protected $policies = [
        ...,
        'Ramnzys\FilamentEmailLog\Models\Email' => 'App\Policies\EmailPolicy'

    ];
```
Same applies for models inside folders.

#### Translations 

Publish the translations using:

```bash
php artisan vendor:publish --tag="filament-shield-translations"
```

## Available Filament Shield Commands

#### `shield:doctor` 
- Show useful info about Filament Shield.

#### `shield:install` 
Setup Core Package requirements and Install Shield. Accepts the following flags:
- `--fresh`           re-run the migrations
- `--only`            Only setups shield without generating permissions and creating super-admin
  
#### `shield:generate`
Generate Permissions and/or Policies for Filament entities. Accepts the following flags: 
- `--all`                    Generate permissions/policies for all entities
- `--option[=OPTION]`        Override the config generator option(`policies_and_permissions`,`policies`,`permissions`)
- `--resource[=RESOURCE]`    One or many resources separated by comma (,)
- `--page[=PAGE]`            One or many pages separated by comma (,)
- `--widget[=WIDGET]`        One or many widgets separated by comma (,)
- `--exclude`                Exclude the given entities during generation
- `--ignore-config-exclude`  Ignore config `exclude` option during generation

#### `shield:super-admin` 
Create a user with super_admin role.
- Accepts an `--user=` argument that will use the provided ID to find the user to be made super admin.

### `shield:publish`
- Publish the Shield `RoleResource` and customize it however you like

### `shield:seeder`
- Deploy easily by setting up your roles and permissions or add your custom seeds
  
#### `shield:upgrade` 
- Upgrade shield.


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
