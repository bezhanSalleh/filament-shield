<a href="https://github.com/bezhansalleh/filament-shield" class="filament-hidden">
<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://repository-images.githubusercontent.com/443775406/08a6c1ef-c8cc-4085-afb3-fb916ce6d3c6" >
</a>

<p align="center" class="flex items-center justify-center">
    <a href="https://filamentadmin.com/docs/2.x/admin/installation">
        <img alt="FILAMENT 4.x" src="https://img.shields.io/badge/FILAMENT-4.x-EBB304?style=for-the-badge">
    </a>
    <a href="https://packagist.org/packages/bezhansalleh/filament-shield">
        <img alt="Packagist" src="https://img.shields.io/packagist/v/bezhansalleh/filament-shield.svg?style=for-the-badge&logo=packagist">
    </a>
    <a href="https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3Arun-tests+branch%3Amain">
        <img alt="Tests Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-shield/run-tests.yml?style=for-the-badge&logo=github&label=tests" class="filament-hidden">
    </a>
    <a href="https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain" class="filament-hidden">
        <img alt="Code Style Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-shield/laravel-pint.yml?style=for-the-badge&logo=github&label=code%20style">
    </a>

<a href="https://packagist.org/packages/bezhansalleh/filament-shield">
    <img alt="Downloads" src="https://img.shields.io/packagist/dt/bezhansalleh/filament-shield.svg?style=for-the-badge" >
    </a>
</p>

# Shield

The easiest and most intuitive way to add access management to your Filament Panels.


> [!IMPORTANT]
> This iteration is a complete rewrite from 3.x|4.x-beta and is not backward compatible. Please refer to the release notes on how to [UPGRADE](https://github.com/bezhanSalleh/filament-shield/releases/tag/4.0.0).



> [!NOTE]
> The Documentation is a work in progress. Please refer to the [CHANGELOG](CHANGELOG.md) and [PR](https://github.com/bezhanSalleh/filament-shield/pull/592) for latest updates.
> Feedbacks and contributions are welcome!

## Features

- 🛡️ **Complete Authorization Management**
  - 📦 Resource Permissions
  - 📄 Page Permissions
  - 🧩 Widget Permissions
- 🛠️ **Custom (ad‑hoc) permissions**
- 🤖 **Automatic Policy Generation**
  - 📜 Default Policy methods for Filament Resources
  - 🏷️ Per Resource Policy definition
  - 🔗 Third-party resource policy & permission generation
- 👑 **Super admin role or gate interception**
- 👤 **Optional baseline panel user role**
- 🔄 **Multi-tenancy Support**
- 🔍 **Entity discovery** (across all panels if enabled)
- 🌐 **Localized permission & entity labels**
- 🌱 **Seeder generation** (roles + direct permissions)
- 🎨 **Best UI**
    - 🖌️ Publish & customize the built-in resource
- ⚡ **Fine-grained CLI tooling** with safe prohibiting

<div class="filament-hidden">

## Compatibility

| Package Version | Filament Version |
|-----------------|------------------|
| [2.x](https://github.com/bezhanSalleh/filament-shield/tree/2.x)             | 2.x              |
| [4.x](https://github.com/bezhanSalleh/filament-shield/tree/3.x)             | 3.x              |
| **4.x**             | 4.x              |

</div>

<div class="filament-hidden">
<b>Table of Contents</b>

- [Shield](#shield)
  - [Features](#features)
  - [Compatibility](#compatibility)
  - [Installation](#installation)
    - [1. Install Package](#1-install-package)
    - [2. Configure Auth Provider](#2-configure-auth-provider)
      - [2.1. Publish the config and set your auth provider model.](#21-publish-the-config-and-set-your-auth-provider-model)
      - [2.2 Add the `HasRoles` trait to your auth provider model:](#22-add-the-hasroles-trait-to-your-auth-provider-model)
    - [3. Setup Shield](#3-setup-shield)
  - [Usage](#usage)
      - [Configuration](#configuration)
    - [Permissions](#permissions)
      - [Customize permission key composition](#customize-permission-key-composition)
      - [Resources](#resources)
        - [Default](#default)
        - [Resource-Specific Methods](#resource-specific-methods)
        - [Custom Permissions](#custom-permissions)
      - [Pages](#pages)
      - [Widgets](#widgets)
    - [Policies ](#policies-)
      - [Users (Assigning Roles to Users)](#users-assigning-roles-to-users)
  - [FilamentShieldPlugin \& RoleResource](#filamentshieldplugin--roleresource)
    - [Navigation](#navigation)
    - [Labels](#labels)
    - [Global Search](#global-search)
    - [Parent Resource](#parent-resource)
    - [Tenancy](#tenancy)
    - [Layout Customization](#layout-customization)
  - [Commands](#commands)
    - [Prohibited Commands](#prohibited-commands)
    - [Core Commands](#core-commands)
    - [Generate Command Options (recap)](#generate-command-options-recap)
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
#### 2.1. Publish the config and set your auth provider model.
```bash
php artisan vendor:publish --tag="filament-shield-config"
```
```php
// config/filament-shield.php
return [
    // ...
    'auth_provider_model' => 'App\\Models\\User',
    // ...
];
```
#### 2.2 Add the `HasRoles` trait to your auth provider model:
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

### 3. Setup Shield
Run the setup command (it is interactive and smart):
```bash
php artisan shield:setup
```

## Usage

#### Configuration
See `config/filament-shield.php`. Key areas:

- `shield_resource` (slug, tabs, cluster, model path visibility)
- `tenant_model` & spatie teams integration
- `auth_provider_model`
- `super_admin` (role name, gate interception)
- `panel_user` baseline role
- `permissions` (separator, case, auto-generate toggle, custom builder)
- `policies` (path, merge, generate, methods lists)
- `localization` (enable + namespace key)
- `resources` (subject mode, manage overrides, exclusions)
- `pages` / `widgets` (prefix, subject, exclusions)
- `custom_permissions` (ad‑hoc)
- `discovery` (multi-panel discovery toggles)
- `register_role_policy`

### Permissions
You can customize how permission keys are generated to match your preferred naming convention and organizational standards. Shield uses these settings from `filament-shield.php` **config** when creating permission names from your `{Resources|Page|Widgets}`.

```php
'permissions' => [
    'separator' => ':',
    'case' => 'pascal',
    'generate' => true,
],
```
* Case options: `camel`, `kebab`, `snake`, `pascal`, `upper_snake` (default: `pascal`)

#### Customize permission key composition
You can customize how permission keys are generated by providing your own callback to `buildPermissionKeyUsing` in your `AppServiceProvider`'s `boot()` method. The callback receives the following parameters:
- `string $entity`: The type of entity (e.g., 'Resource', 'Page', 'Widget').
- `string $affix`: The action or method name (e.g., 'viewAny', 'create').
- `string $subject`: The subject or resource name (e.g., 'Post', 'Dashboard').
- `string $case`: The case format specified in the config (e.g., 'pascal').
- `string $separator`: The separator specified in the config (e.g., ':').

* Now let's consider an example where we want to handle `Resource` entities which handles the same `Model` or `Models` with same name but with different namespace and directory structure. The Filament [Demo]() has two resources with the same name which handles two different models:
  - `App\Filament\Resources\Blog\Categories\CategoryResource` which handles `App\Models\Blog\Category`
  - `App\Filament\Resources\Shop\Categories\CategoryResource` which handles `App\Models\Shop\Category`
  
  By default Shield will generate the same permission keys for both resources which can cause conflicts. To avoid this we can customize the permission key composition to include the navigation group of the resource as part of the permission key. Here's how you can do it:
  ```php
  use BezhanSalleh\FilamentShield\Facades\FilamentShield;
  use Filament\Resources\Resource;
  use Filament\Pages\BasePage as Page;
  use Filament\Widgets\Widget;

  FilamentShield::buildPermissionKeyUsing(
      function (string $entity, string $affix, string $subject, string $case, string $separator) {
        if (in_array(
              needle: $entity, 
              haystack: [
                  'App\Filament\Resources\Blog\Categories\CategoryResource',
                  'App\Filament\Resources\Shop\Categories\CategoryResource'
              ]
              strict: true
        )) {
            $resource = resolve($entity);
            $subject = str($subject)
                ->prepend($resource::getNavigationGroup())
                ->trim()
                ->toString();
        }

        return FilamentShield::defaultPermissionKeyBuilder(
            affix: $affix, 
            separator: $separator, 
            subject: $subject, 
            case: $case
        );
    }
  );
  ```
  Now when you run the `shield:generate` command, it will generate distinct permission keys for each `CategoryResource` based on their navigation groups:
  - For `Blog` CategoryResource:
    - `ViewAny:BlogCategories`
    - `View:BlogCategories`
    - `Create:BlogCategories`
    - `Update:BlogCategories`
    - `Delete:BlogCategories`
  - For `Shop` CategoryResource since it uses a cluster then its navigation group is blanked so it will just use the resource name:
    - `ViewAny:Categories`
    - `View:Categories`
    - `Create:Categories`
    - `Update:Categories`
    - `Delete:Categories`
  
This approach ensures that each resource has a unique set of permission keys, preventing any conflicts and allowing for more granular access control. You can ofcourse extract the logic to a separate class or function if it gets too complex but this should give you a good starting point.

#### Resources
Shield derives resource permission keys from configured policy methods.

##### Default
Add or remove global policy method names under `policies.methods`. Re-run `shield:generate` to sync roles/permissions. Example additions: `lock`, `archive`.

##### Resource-Specific Methods
Provide overrides/additions per resource via `resources.manage`:
```php
'resources' => [
    'manage' => [
        \App\Filament\Resources\PostResource::class => [
            'viewAny','view','create','update','delete','publish',
        ],
    ],
],
```
`publish` will be merged with defaults when `policies.merge = true`.

##### Custom Permissions
Add ad‑hoc permissions under `custom_permissions`:
```php
'custom_permissions' => [
    'Impersonate:User' => 'Impersonate User',
    'Export:Order' => 'Export Orders',
],
```
They appear in the Role resource (Custom Permissions tab) when enabled.

#### Pages

If you have generated permissions for `Pages` you can toggle the page's navigation from sidebar and restrict access to the page. You can set this up manually but this package comes with a `HasPageShield` trait to speed up this process. All you have to do is drop in the trait to your pages:
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

#### Widgets

if you have generated permissions for `Widgets` you can toggle their state based on whether a user have permission or not. You can set this up manually but this package comes with a `HasWidgetShield` trait to speed up this process. All you have to do is drop in the trait to your widgets:
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

### Policies <!-- TODO: Document policy generation -->

Shield generates policies to `policies.path`. Add or remove method names under `policies.methods`. Set `policies.merge` to control merging with per‑resource overrides. Existing policy files are skipped unless you use `--ignore-existing-policies` when generating.

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

## FilamentShieldPlugin & RoleResource
The plugin exposes a couple of methods to handle resource related customizations and overrides without publishing the resource. You can use the plugin as following:

### Navigation
You may use the following methods to customize the navigation of the `RoleResource`:

```php
FilamentShieldPlugin::make()
    ->navigationLabel('Label')                  // string|Closure|null
    ->navigationIcon('heroicon-o-home')         // string|Closure|null  
    ->activeNavigationIcon('heroicon-s-home')   // string|Closure|null
    ->navigationGroup('Group')                  // string|Closure|null
    ->navigationSort(10)                        // int|Closure|null
    ->navigationBadge('5')                      // string|Closure|null
    ->navigationBadgeColor('success')           // string|array|Closure|null
    ->navigationParentItem('parent.item')       // string|Closure|null
    ->registerNavigation();                     // bool|Closure
```

### Labels
You may use the following methods to customize the labels of the `RoleResource`:

```php
FilamentShieldPlugin::make()
    ->modelLabel('Model')                       // string|Closure|null
    ->pluralModelLabel('Models')                // string|Closure|null
    ->recordTitleAttribute('name')              // string|Closure|null
    ->titleCaseModelLabel(false);               // bool|Closure

```

### Global Search
You may use the following methods to customize the global search related functionality of the `RoleResource`:

```php
FilamentShieldPlugin::make()
    ->globallySearchable(true)                  // bool|Closure
    ->globalSearchResultsLimit(50)              // int|Closure
    ->forceGlobalSearchCaseInsensitive(true)    // bool|Closure|null
    ->splitGlobalSearchTerms(false);            // bool|Closure
```

### Parent Resource
You may use the following method to set a parent resource for the `RoleResource`:

```php
FilamentShieldPlugin::make()
    ->parentResource(ParentResource::class);    // string|Closure|null
```

### Tenancy
You may use the following methods to customize the tenancy related functionality of the `RoleResource`:

```php
FilamentShieldPlugin::make()
    ->scopeToTenant(true)                       // bool|Closure
    ->tenantRelationshipName('organization')    // string|Closure|null
    ->tenantOwnershipRelationshipName('owner'); // string|Closure|null
```

### Layout Customization
1. You can easily customize the `Grid`, `Section` and `CheckboxList`'s `columns()` and `columnSpan()` without publishing the resource.
   ```php
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
   ```
   <img width="1161" alt="Screenshot 2023-09-24 at 10 34 31 PM" src="https://github.com/bezhanSalleh/filament-shield/assets/10007504/be42bab2-72d1-4db0-8de4-8b8fba2d4e68">

2. You can also make the resource tab to have a simple view like the other tabs by using the following method:

   ```php
      FilamentShieldPlugin::make()
          ->simpleResourcePermissionView()
    ```

3. When you have localization enabled and setup and you want the permission labels to react to your application's chosen locale/language you can use the following method:

   ```php
   FilamentShieldPlugin::make()
       ->localizePermissionLabels()
   ```

## Commands

### Prohibited Commands

Since almost all shield commands are destructive and can cause data loss, they can be prohibited by calling the prohibit method of the command as following in a service provider's `boot()` method:
```php
use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Commands;
    public function boot(): void
    {
        // individually prohibit commands
        Commands\GenerateCommand::prohibit($this->app->isProduction());
        Commands\InstallCommand::prohibit($this->app->isProduction());
        Commands\PublishCommand::prohibit($this->app->isProduction());
        Commands\SetupCommand::prohibit($this->app->isProduction());
        Commands\SeederCommand::prohibit($this->app->isProduction());
        Commands\SuperAdminCommand::prohibit($this->app->isProduction());
        // or prohibit the above commands all at once
        FilamentShield::prohibitDestructiveCommands($this->app->isProduction())
    }
```

### Core Commands

```bash
shield:setup [--fresh] [--tenant=] [--force] [--starred]

shield:install {panel} [--tenant]

shield:generate [--all] [--option=] [--resource=] [--page=] [--widget=] [--exclude] [--ignore-existing-policies] [--panel=] [--relationships]

shield:super-admin [--user=] [--panel=] [--tenant=]

shield:seeder [--generate] [--option=permissions_via_roles|direct_permissions] [--force]

shield:publish --panel={panel} [--cluster=] [--nested] [--force]
```

### Generate Command Options (recap)

```bash
--all  Generate for all discovered entities
--option=policies_and_permissions|policies|permissions|tenant_relationships Override generator mode
--resource=FooResource,BarResource  Target resources (class basenames)
--page=Dashboard,Settings           Target pages (basenames)
--widget=StatsOverview,SalesChart   Target widgets (basenames)
--exclude                           Treat provided entities as exclusions
--ignore-existing-policies          Force regeneration of already existing policies
--panel=admin                       Panel ID (required when not interactive)
--relationships                     Generate tenancy relationships (panel must have tenancy)
```

## Translations 

Publish the translations using:

```bash
php artisan vendor:publish --tag="filament-shield-translations"
```

## Testing

```bash
composer test
```

## Changelog

See [CHANGELOG](CHANGELOG.md).

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bezhan Salleh](https://github.com/bezhanSalleh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
