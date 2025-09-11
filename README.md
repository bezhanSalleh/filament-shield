<a href="https://github.com/bezhansalleh/filament-shield" class="filament-hidden">
<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://repository-images.githubusercontent.com/443775406/08a6c1ef-c8cc-4085-afb3-fb916ce6d3c6" >
</a>

<p align="center" class="flex items-center justify-center">
    <a href="https://filamentphp.com/docs/4.x/panels/installation">
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

<span style="font-size:2em; font-weight:bold; display:block; margin:0.67em 0;">Shield</span>

The easiest and most intuitive way to add access management to your Filament panels.


> [!IMPORTANT]
> This iteration is a complete rewrite from versions 3.x and 4.x-beta and is not backward compatible. Please refer to the release notes on how to [UPGRADE](https://github.com/bezhanSalleh/filament-shield/releases/tag/4.0.0).



> [!NOTE]
> The documentation is a work in progress. Please refer to the [CHANGELOG](CHANGELOG.md) and [PR](https://github.com/bezhanSalleh/filament-shield/pull/592) for the latest updates.
> Feedback and contributions are welcome!

## Features

- 🛡️ **Complete Authorization Management**
  - 📦 Resource Permissions
  - 📄 Page Permissions
  - 🧩 Widget Permissions
- 🛠️ **Custom (ad-hoc) permissions**
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
- 🎨 **Intuitive UI**
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

- [Installation](#installation)
  - [1. Install Package](#1-install-package)
  - [2. Configure Auth Provider](#2-configure-auth-provider)
  - [3. Setup Shield](#3-setup-shield)
- [Usage \& Configuration](#usage--configuration)
  - [Permissions](#permissions)
    - [Configuration](#configuration)
    - [Case](#case)
    - [Customize permission key composition](#customize-permission-key-composition)
  - [Policies](#policies)
    - [Configuration](#configuration-1)
    - [Methods](#methods)
    - [Merge](#merge)
    - [Single Parameter Methods](#single-parameter-methods)
    - [Policy Enforcement](#policy-enforcement)
  - [Resources](#resources)
    - [Configuration](#configuration-2)
    - [Subject](#subject)
    - [Manage](#manage)
    - [Exclude](#exclude)
  - [Pages \& Widgets](#pages--widgets)
    - [Configuration](#configuration-3)
    - [Options](#options)
    - [Permission Enforcement](#permission-enforcement)
  - [Custom Permissions](#custom-permissions)
  - [Users (Assigning Roles to Users)](#users-assigning-roles-to-users)
  - [Shield Plugin \& Resource](#shield-plugin--resource)
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
  - [Localization](#localization)
    - [Configuration](#configuration-4)
    - [Key](#key)
    - [Default](#default)
- [Translations](#translations)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)
</div>

# Installation

## 1. Install Package
```bash
composer require bezhansalleh/filament-shield
```

## 2. Configure Auth Provider
1. Publish the config and set your auth provider model.
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
2. Add the `HasRoles` trait to your auth provider model:
   ```php
   use Spatie\Permission\Traits\HasRoles;

   class User extends Authenticatable
   {
       use HasRoles;
   }
   ```

## 3. Setup Shield
Run the setup command (it is interactive and smart):
```bash
php artisan shield:setup
```

# Usage & Configuration
The package comes with a sensible default configuration that should work for most applications. You can customize the configuration by modifying it to fit your needs. The following sections explain the various configuration options available.

## Permissions
You can customize how permission keys are generated to match your preferred naming conventions and organizational standards. Shield uses these settings from the `filament-shield.php` **config** file when creating permission names from your `{Resources|Pages|Widgets}`.

### Configuration
```php
'permissions' => [
    'separator' => ':',
    'case' => 'pascal',
    'generate' => true,
],
```
### Case
Shield formats permission keys using the specified case style. The available options are:
- `camel`
- `kebab` 
- `snake`
- `pascal` (default)
- `upper_snake`

### Customize permission key composition
You can customize how permission keys are generated by providing your own callback to `buildPermissionKeyUsing` in your `AppServiceProvider`'s `boot()` method. The callback receives the following parameters:
- `string $entity`: The type of entity (e.g., 'Resource', 'Page', 'Widget').
- `string $affix`: The action or method name (e.g., 'viewAny', 'create').
- `string $subject`: The subject or resource name (e.g., 'Post', 'Dashboard').
- `string $case`: The case format specified in the config (e.g., 'pascal').
- `string $separator`: The separator specified in the config (e.g., ':').

* Now let's consider an example where we want to handle `Resource` entities that handle the same `Model` or `Models` with the same name but with different namespaces and directory structures. The Filament [Demo](https://github.com/filamentphp/demo) has two resources with the same name that handle two different models:
  - `App\Filament\Resources\Blog\Categories\CategoryResource` that handles `App\Models\Blog\Category`
  - `App\Filament\Resources\Shop\Categories\CategoryResource` that handles `App\Models\Shop\Category`
  
  By default Shield will generate the same permission keys for both resources which can cause conflicts. To avoid this we can customize the permission key composition to include the navigation group of the resource as part of the permission key. Here's how you can do it:
  ```php
  use BezhanSalleh\FilamentShield\Facades\FilamentShield;
  use Filament\Resources\Resource;

  FilamentShield::buildPermissionKeyUsing(
      function (string $entity, string $affix, string $subject, string $case, string $separator) {
        if (is_subclass_of($entity, Resource::class) && in_array(
              needle: $entity, 
              haystack: [
                  'App\Filament\Resources\Blog\Categories\CategoryResource',
                  'App\Filament\Resources\Shop\Categories\CategoryResource'
              ],
              strict: true
        )) {
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
  - For `Blog`'s `CategoryResource` since its navigation group is `Blog`:
    - `ViewAny:BlogCategories`
    - `View:BlogCategories`
    - `Create:BlogCategories`
    - `Update:BlogCategories`
    - `Delete:BlogCategories`
  - For `Shop`'s `CategoryResource` since it uses a cluster and its navigation group is blank, so it will just use the resource `subject` configured in the config `filament-shield.resources.subject` which is `model` by default:
    - `ViewAny:Categories`
    - `View:Categories`
    - `Create:Categories`
    - `Update:Categories`
    - `Delete:Categories`
  
This approach ensures that each resource has a unique set of permission keys, preventing any conflicts and allowing for more granular access control. You can of course extract the logic to a separate class or function if it gets too complex, but this should give you a good starting point.

## Policies
Shield automatically generates policies for your Resources' Models.

### Configuration
```php
'policies' => [
    'path' => app_path('Policies'),
    'merge' => true,
    'generate' => true,
    'methods' => [
        'viewAny', 'view', 'create', 'update', 'delete', 'restore',
        'forceDelete', 'forceDeleteAny', 'restoreAny', 'replicate', 'reorder',
    ],
    'single_parameter_methods' => [
        'viewAny',
        'create',
        'deleteAny',
        'forceDeleteAny',
        'restoreAny',
        'reorder',
    ],
],
```

### Methods
Each policy includes methods defined in the `policies.methods` config. You can customize this list to fit your application's needs. Since Filament Resources typically use a standard set of methods, the default configuration should suffice for most applications. If you have specific resources that require additional methods, you can easily add them to the list. 
However, it would be best to only include methods that are commonly used across your resources and define any resource-specific methods in the `resources.manage` config section. This approach keeps your policies clean and relevant to your application's requirements.

### Merge
When `policies.merge` is set to `true`, Shield will combine the global methods defined in `policies.methods` with any resource-specific methods you define in `resources.manage`. This ensures that each resource's policy includes both the standard methods and any additional ones you need for that particular resource.

### Single Parameter Methods
Some policy methods only require the user instance as a parameter (e.g., `viewAny`, `create`). These are defined in `policies.single_parameter_methods`. Shield will generate these methods accordingly in the policies. When you add new methods or resource-specific methods, ensure to update this list if they also only require the user instance. This helps maintain consistency and clarity in your policy definitions.

### Policy Enforcement
Laravel automatically resolves policies for models, but this is not always the case. For instance, if your models are not in the default `App\Models` namespace, are nested, or are from third-party plugins, you may need to manually register the policies. You can do this in a service provider's `boot()` method: 

```php
Gate::policy(Awcodes\Curator\Models\Media::class, App\Policies\MediaPolicy::class);
```

**Tip** For your in-app resources' models you can add the following method in the `boot()` method to automatically enforce policies, without the need to manually register each policy. This assumes your policies are in the `App\Policies` namespace and follow the naming convention of appending `Policy` to the model class name. Adjust the `str_replace` parameters if your structure differs:

```php
use Illuminate\Support\Facades\Gate;

Gate::guessPolicyNamesUsing(function (string $modelClass) {
    return str_replace('Models', 'Policies', $modelClass) . 'Policy';
});
```

## Resources
Shield derives resource permission keys from configured policy methods. Since Filament `Resources`' authorization is handled via policies, generated permissions align with policy methods.

### Configuration
```php
'resources' => [
    'subject' => 'model',
    'manage' => [
        \BezhanSalleh\FilamentShield\Resources\Roles\RoleResource::class => [
            'viewAny',
            'view',
            'create',
            'update',
            'delete',
        ],
    ],
    'exclude' => [
        //
    ],
],
```
### Subject
You can customize the subject used for resource permissions by setting the `subject` key in the `resources` configuration. The subject can be set to either `class` or `model` (default is `model`).

### Manage
You can define resource-specific policy methods in the `resources.manage` configuration. This allows you to tailor the permissions for individual resources (in-app or third-party) based on their unique requirements. When you specify methods here, Shield will generate permissions for these methods in addition to the global methods defined in `policies.methods`, provided that `policies.merge` is set to `true`. This ensures that each resource has a comprehensive set of permissions that reflect both standard and resource-specific actions.

### Exclude
You can exclude specific resources from permission generation by listing them in the `resources.exclude` configuration. This is useful for resources that should always be accessible or do not require permission checks. When a resource is excluded, Shield will skip generating permissions and policy for it.

## Pages & Widgets  

Both **pages** and **widgets** in Filament follow a similar permission model. By default, they require **view** permissions. You can customize their behavior in the configuration, including subject, prefix, exclusions, and enforcement traits.  

### Configuration  

**Pages**  
```php
'pages' => [
    'subject' => 'class',
    'prefix' => 'view',
    'exclude' => [
        \Filament\Pages\Dashboard::class,
    ],
],
```

**Widgets**  
```php
'widgets' => [
    'subject' => 'class',
    'prefix' => 'view',
    'exclude' => [
        \Filament\Widgets\AccountWidget::class,
        \Filament\Widgets\FilamentInfoWidget::class,
    ],
],
```

### Options  

| Option     | Description |
|------------|-------------|
| **Subject** | Determines how the permission subject is generated. <br>• `class` → Uses the class name (default). <br>• `model` → Uses the model name (if the entity has a `static getModel()` method). |
| **Prefix**  | Prepended to permission keys for distinction. <br>• Example for Pages: `Page:IconLibrary` <br>• Example for Widgets: `Widget:IncomeWidget`. |
| **Exclude** | Entities listed here will be skipped during permission generation. <br>Useful for always-accessible entities like dashboards, account widgets, or system info. |


### Permission Enforcement  

Use the appropriate **Shield trait** to automatically enforce permissions. 
When applied, these traits ensure:  
- Navigation or rendering is **hidden** if the user lacks permission.  
- Access to the page/widget is **restricted**.   

**Pages**  
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

**Widgets**  
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


## Custom Permissions
Most of the time you will have some ad-hoc permissions that don't fit into the `Resource`, `Page`, or `Widget` categories, or you might not want a policy method for them. You can define these under `custom_permissions` in the config:
```php
'custom_permissions' => [
    'Impersonate:User' => 'Impersonate User',
    'Export:Order' => 'Export Orders',
],
```
They appear in the `Role Resource`'s **Custom Permissions** tab when enabled.
To enable the tab, set `shield_resource.tabs.custom_permissions` to `true` in the config.

## Users (Assigning Roles to Users)
Shield does not come with a way to assign roles to your users out of the box; however, you can easily assign roles to your users using Filament's `Forms` `Select` or `CheckboxList` component. Inside your users `Resource`'s form, add one of these components and configure them as needed:
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

## Shield Plugin & Resource
The plugin provides several methods to handle resource-related customizations and overrides without publishing the resource. You can use the plugin as follows:

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

Since almost all Shield commands are destructive and can cause data loss, they can be prohibited by calling the `prohibit` method of the command as follows in a service provider's `boot()` method:
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
        FilamentShield::prohibitDestructiveCommands($this->app->isProduction());
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

## Localization
Shield supports multiple languages out of the box. When enabled, you can provide translated labels for 
permissions to create a more localized experience for your international users.

### Configuration
```php
'localization' => [
     'enabled' => false,
     'key' => 'filament-shield::filament-shield',
 ],
```
### Key
You can translate the permission labels by creating the translations files for your application's 
supported locales following Laravel's localization conventions. The translation file can be 
named anything you want. 
For example, you can create a file named `permissions.php` per locale and then set the 
`localization.key` in the config as `localization.key' => 'permissions'`. 
For the default permission pattern, the structure of the translation file could be as follow:

```php
return [
    'ViewAny:Posts' => 'View All Posts',
    'View:Posts' => 'View Post',
    'Create:Posts' => 'Create Post',
    'Update:Posts' => 'Update Post',
    'Delete:Posts' => 'Delete Post',
];
```

### Default
if you want to use the default translations provided by the package for the commonly used set of permissions for resources, you can set the `localization.key` in the config as `localization.key' => 'filament-shield::filament-shield.resource_permission_prefixes_labels'` and enable localization by setting `localization.enabled` to `true`.

# Translations 

Publish the translations using:

```bash
php artisan vendor:publish --tag="filament-shield-translations"
```

# Testing

```bash
composer test
```

# Changelog

See [CHANGELOG](CHANGELOG.md).

# Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

# Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

# Credits

- [Bezhan Salleh](https://github.com/bezhanSalleh)
- [All Contributors](../../contributors)

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
