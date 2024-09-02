<a href="https://github.com/bezhansalleh/filament-shield" class="filament-hidden">
<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://user-images.githubusercontent.com/10007504/148662315-35d4bd74-fc1c-4f8c-8c02-689309b414b0.png" >
</a>

<p align="center" class="flex items-center justify-center">
    <a href="https://filamentadmin.com/docs/2.x/admin/installation">
        <img alt="FILAMENT 8.x" src="https://img.shields.io/badge/FILAMENT-3.x-EBB304?style=for-the-badge">
    </a>
    <a href="https://packagist.org/packages/bezhansalleh/filament-shield">
        <img alt="Packagist" src="https://img.shields.io/packagist/v/bezhansalleh/filament-shield.svg?style=for-the-badge&logo=packagist">
    </a>
    <a href="https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3Arun-tests+branch%3A3.x">
        <img alt="Tests Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-shield/run-tests.yml?style=for-the-badge&logo=github&label=tests" class="filament-hidden">
    </a>
    <a href="https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3A3.x" class="filament-hidden">
        <img alt="Code Style Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-shield/laravel-pint.yml?style=for-the-badge&logo=github&label=code%20style">
    </a>

<a href="https://packagist.org/packages/bezhansalleh/filament-shield">
    <img alt="Downloads" src="https://img.shields.io/packagist/dt/bezhansalleh/filament-shield.svg?style=for-the-badge" >
    </a>
</p>

# Shield

The easiest and most intuitive way to add access management to your Filament Admin:
- :fire: **Resources** 
- :fire: **Pages** 
- :fire: **Widgets** 
- :fire: **Custom Permissions**


> [!NOTE] 
> For **Filament 2.x** use **[2.x](https://github.com/bezhanSalleh/filament-shield/tree/2.x)** branch

> [!IMPORTANT]
> Prior to `v3.1.0` Shield supported [spatie/laravel-permission](https://packagist.org/packages/spatie/laravel-permission):`^5.0` and now it supports version `^6.0`. Which has some breaking changes around migrations. If you are upgrading from a version prior to `v3.1.0` please make sure to remove the old migration file and republish the new one.

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
    use HasRoles;

    // ...
}
```
3. Publish the `config` file then setup your configuration:
```bash
php artisan vendor:publish --tag=filament-shield-config
```
4. Register the plugin for the Filament Panels you want
```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
        ]);
}
```
5. Now run the following command to install shield:
```bash
php artisan shield:install
```
Follow the prompts and enjoy!

## Filament Panels
If you want to enable `Shield` for more than one panel then you need to register the plugin for each panel as mentioned above.

### Panel Access
Shield comes with the `HasPanelShield` trait which provides an easy way to integrate Shield's conventions with the Filament's panel access system.

The `HasPanelShield` trait provides an implementation for the `canAccessPanel` method, determining access based on whether the user possesses the `super_admin` role or the `panel_user` role. It also assigns the `panel_user` role to the user upon creation and removes it upon deletion. Ofcourse the role names can be changed from the plugin's configuration file.

```php
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;
    use HasPanelShield;
    // ...
}
```
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
apply this to each language you have groups in.

#### Pages

If you have generated permissions for `Pages` you can toggle the page's navigation from sidebar and restrict access to the page. You can set this up manually but this package comes with a `HasPageShield` trait to speed up this process. All you have to do is use the trait in you pages:
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

### Policies

#### Role Policy
##### Using Laravel 10
To ensure `RoleResource` access via `RolePolicy` you would need to add the following to your `AuthServiceProvider`:

```php
//AuthServiceProvider.php
...
protected $policies = [
    'Spatie\Permission\Models\Role' => 'App\Policies\RolePolicy',
];
...
```
##### Using Laravel 11

To ensure `RoleResource` access via `RolePolicy` you would need to add the following to your `AppServiceProvider`:

```php
//AppServiceProvider.php
use Illuminate\Support\Facades\Gate;
...
public function boot(): void
    {
        ...
        Gate::policy(\Spatie\Permission\Models\Role::class, \App\Policies\RolePolicy::class);
    }
...
```

**whatever your version of Laravel, you can skip it if you have enabled it from the `config`:**

```php
// config/filament-shield.php
...

'register_role_policy' => [
    'enabled' => true,
],
...
```

#### Policy Path
If your policies are not in the default `Policies` directory in the `app_path()` you can change the directory name in the config file:

```php
...
'generator' => [
    'option' => 'policies_and_permissions',
    'policy_directory' => 'Policies',
],
...
```

#### Custom folder structure for Models or Third-Party Plugins

Shield also generates policies and permissions for third-party plugins and `Models` with custom folder structure and to enforce the generated policies you will need to register them.
##### Using Laravel 10
```php
//AuthServiceProvider.php
...
class AuthServiceProvider extends ServiceProvider
{
    ...
    protected $policies = [
        ...,
        'App\Models\Blog\Author' => 'App\Policies\Blog\AuthorPolicy',
        'Ramnzys\FilamentEmailLog\Models\Email' => 'App\Policies\EmailPolicy'

    ];
```
##### Using Laravel 11
```php
//AppServiceProvider.php
use Illuminate\Support\Facades\Gate;
...
class AppServiceProvider extends ServiceProvider
{
    ...
   public function boot(): void
    {
        ...
        Gate::policy(\App\Models\Blog\Author::class, \App\Policies\Blog\AuthorPolicy::class);
        Gate::policy(\Ramnzys\FilamentEmailLog\Models\Email::class, \App\Policies\EmailPolicy::class);
    }
```
#### Users (Assigning Roles to Users)
Shield does not come with a way to assign roles to your users out of the box, however you can easily assign roles to your users using Filament `Forms`'s `Select` or `CheckboxList` component. Inside your users `Resource`'s form add one of these components and configure them as you need:
```php
// Using Select Component
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()
    ->preload()
    ->searchable()
                    
// Using CheckboxList Component
Forms\Components\CheckboxList::make('roles')
    ->relationship('roles', 'name')
    ->searchable()
```

You can find out more about these components in the [Filament Docs](https://filamentphp.com/docs/3.x/forms/installation)

- [Select](https://filamentphp.com/docs/3.x/forms/fields/select)
- [CheckboxList](https://filamentphp.com/docs/3.x/forms/fields/checkbox-list)

#### Layout Customization
You can easily customize the `Grid`, `Section` and `CheckboxList`'s `columns()` and `columnSpan()` without publishing the resource.
```php
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

public function panel(Panel $panel): Panel
{
        return $panel
            ...
            ...
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ]);
}
```
<img width="1161" alt="Screenshot 2023-09-24 at 10 34 31 PM" src="https://github.com/bezhanSalleh/filament-shield/assets/10007504/be42bab2-72d1-4db0-8de4-8b8fba2d4e68">

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
- `--ignore-existing-policies`  Do not overwrite the existing policies.

#### `shield:super-admin` 
Create a user with super_admin role.
- Accepts an `--user=` argument that will use the provided ID to find the user to be made super admin.

### `shield:publish`
- Publish the Shield `RoleResource` and customize it however you like

### `shield:seeder`
- Deploy easily by setting up your roles and permissions or add your custom seeds
  

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
