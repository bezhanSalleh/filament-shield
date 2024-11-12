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

The easiest and most intuitive way to add access management to your Filament Panels.

## Features

- 🛡️ **Complete Authorization Management**
  - Resource Permissions
  - Page Permissions
  - Widget Permissions
  - Custom Permissions
- 🔄 **Multi-tenancy Support**
- 🚀 **Easy Setup & Configuration**
- 🎨 **Best UI**
- 📦 **Policy Generation**
- 🌐 **Translations Support**

## Requirements

- PHP 8.1 | 8.2 | 8.3
- Laravel v10.x | v11.x
- Filament v3.2+
- Spatie Permission v6.0+

> [!NOTE] 
> For **Filament 2.x** use **[2.x](https://github.com/bezhanSalleh/filament-shield/tree/2.x)** branch

<div class="filament-hidden">
<b>Table of Contents</b>

- [Shield](#shield)
  - [Features](#features)
  - [Requirements](#requirements)
  - [Installation](#installation)
    - [1. Install Package](#1-install-package)
    - [2. Configure Auth Provider](#2-configure-auth-provider)
    - [3. Setup Shield](#3-setup-shield)
    - [4. Install for Panel](#4-install-for-panel)
  - [Usage](#usage)
      - [Configuration](#configuration)
      - [Resources](#resources)
        - [Default](#default)
        - [Custom Permissions](#custom-permissions)
        - [Configure Permission Identifier](#configure-permission-identifier)
        - [Custom Navigation Group](#custom-navigation-group)
      - [Pages](#pages)
          - [Pages Hooks](#pages-hooks)
          - [Pages Redirect Path](#pages-redirect-path)
      - [Widgets](#widgets)
    - [Policies](#policies)
      - [Path](#path)
      - [Custom folder structure for Models or Third-Party Plugins](#custom-folder-structure-for-models-or-third-party-plugins)
        - [Using Laravel 10](#using-laravel-10)
        - [Using Laravel 11](#using-laravel-11)
      - [Users (Assigning Roles to Users)](#users-assigning-roles-to-users)
      - [Layout Customization](#layout-customization)
  - [Available Commands](#available-commands)
    - [Prohibited Commands](#prohibited-commands)
    - [Core Commands](#core-commands)
    - [Generate Command Options](#generate-command-options)
      - [Translations](#translations)
  - [Testing](#testing)
  - [Changelog](#changelog)
  - [Contributing](#contributing)
  - [Security Vulnerabilities](#security-vulnerabilities)
  - [Credits](#credits)
  - [License](#license)
</div>

## Installation

### 1. Install Package
```bash
composer require bezhansalleh/filament-shield
```

### 2. Configure Auth Provider
Add the `HasRoles` trait to your User model:
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

### 3. Setup Shield
3.1 **Without Tenancy:**
```bash
php artisan shield:setup
```

3.2 **With Tenancy:**
```bash
php artisan shield:setup --tenant=App\\Models\\Team
# Replace Team with your tenant model
```

This command will:
- Publish core package config
- Publish core package migrations
- Run initial migrations
- Publish shield config
- Configure tenancy if specified

### 4. Install for Panel
The install command will register the plugin for your panel automatically. Choose the appropriate installation method:

4.1 **Without Tenancy:**
```bash
php artisan shield:install admin
# Replace 'admin' with your panel ID
```
Or instead of the above command you can register the plugin manually in your `xPanelProvider`:
```php
    ->plugins([
        \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
    ])
```
4.2 **With Tenancy:**
```bash
php artisan shield:install admin --tenant --generate-relationships
# Replace 'admin' with your panel ID
```
Or instead of the above command you can register the plugin and enable tenancy manually in your `xPanelProvider`:
```php
    ->tenant(YourTenantModel::class)
    ->tenantMiddleware([
        \BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant::class,
    ], isPersistent: true)
    ->plugins([
        \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
    ])
```
This command will:
- Register Shield plugin for your panel
- If `--tenant` flag is provided:
  - Activates tenancy features 
  - Makes the panel tenantable
  - Adds `SyncShieldTenant` middleware to the panel
  - Configures tenant model from the config
- If `--generate-relationships` flag is provided:
  - Generates required relationships between resource models and the tenant model
  - Adds necessary relationship methods in both the resource and tenant models

## Usage

#### Configuration
See [config/filament-shield.php](config/filament-shield.php) for full configuration options.

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

#### Path
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
1. **Without Tenancy**
```php
// Using Select Component
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()
    ->preload()
    ->searchable(),
                    
// Using CheckboxList Component
Forms\Components\CheckboxList::make('roles')
    ->relationship('roles', 'name')
    ->searchable(),
```
2. **With Tenancy**
```php
// Using Select Component
Forms\Components\Select::make('roles')
      ->relationship('roles', 'name')
      ->saveRelationshipsUsing(function (Model $record, $state) {
           $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
      })
     ->multiple()
     ->preload()
     ->searchable(),

// Using CheckboxList Component
Forms\Components\CheckboxList::make('roles')
      ->relationship(name: 'roles', titleAttribute: 'name')
      ->saveRelationshipsUsing(function (Model $record, $state) {
           $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
      })
     ->searchable(),
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

## Available Commands
### Prohibited Commands
Since almost all shield commands are destructive and can cause data loss, they can be prohibited by calling the prohibit method of the command as following in a service provider's `boot()` method:
```php
use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Commands;
    public function boot(): void
    {
        // individually prohibit commands
        Commands\SetupCommand::prohibit($this->app->isProduction());
        Commands\InstallCommand::prohibit($this->app->isProduction());
        Commands\GenerateCommand::prohibit($this->app->isProduction());
        Commands\PublishCommand::prohibit($this->app->isProduction());
        // or prohibit the above commands all at once
        FilamentShield::prohibitDestructiveCommands($this->app->isProduction())
    }
```
### Core Commands
```bash
# Setup Shield
shield:setup [--fresh] [--minimal] [--tenant=]

# Install Shield for a panel
shield:install {panel} [--tenant] [--generate-relationships]

# Generate permissions/policies
shield:generate [options]

# Create super admin
shield:super-admin [--user=] [--panel=] [--tenant=]

# Create seeder
shield:seeder [options]

# Publish Role Resource
shield:publish {panel}
```

### Generate Command Options
```bash
--all                     Generate for all entities
--option=[OPTION]         Override generator option
--resource=[RESOURCE]     Specific resources
--page=[PAGE]            Specific pages  
--widget=[WIDGET]        Specific widgets
--exclude                Exclude entities
--ignore-config-exclude  Ignore config excludes
--panel[=PANEL]          Panel ID to get the components(resources, pages, widgets)
```
> [!NOTE] 
> For setting up super-admin user when using tenancy/team feature consult the core package **[spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/basic-usage/teams-permissions)**

#### Translations 

Publish the translations using:

```bash
php artisan vendor:publish --tag="filament-shield-translations"
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
