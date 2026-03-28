<a href="https://github.com/bezhansalleh/filament-shield" class="filament-hidden">
<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://repository-images.githubusercontent.com/443775406/08a6c1ef-c8cc-4085-afb3-fb916ce6d3c6" >
</a>

<p align="center" class="flex items-center justify-center">
    <a href="https://filamentphp.com/docs/4.x/panels/installation">
        <img alt="FILAMENT 4.x" src="https://img.shields.io/badge/FILAMENT-4.x-EBB304?style=for-the-badge">
    </a>
    <a href="https://filamentphp.com/docs/5.x/panels/installation">
        <img alt="FILAMENT 5.x" src="https://img.shields.io/badge/FILAMENT-5.x-EBB304?style=for-the-badge">
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

<h1 style="font-size:2em; font-weight:bold; display:block; margin:0.67em 0;">Shield</h1>

The easiest and most intuitive way to add access management to your Filament panels.


> [!IMPORTANT]
> This iteration is a complete rewrite from versions 3.x and 4.x-beta and is not backward compatible. Please refer to the [Upgrade](#upgrade) section on how to proceed.


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
| [3.x](https://github.com/bezhanSalleh/filament-shield/tree/3.x)             | 3.x              |
| **4.x**             | 4.x & 5.x             |

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
    - [Policy Placement](#policy-placement)
    - [Skipping Provided Policies](#skipping-provided-policies)
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
    - [Seeder Command Options (recap)](#seeder-command-options-recap)
    - [Custom Super Admin Creation](#custom-super-admin-creation)
  - [Localization](#localization)
    - [Configuration](#configuration-4)
    - [How It Works](#how-it-works)
    - [Generating Translation Files](#generating-translation-files)
    - [Translation Keys](#translation-keys)
    - [Default Package Translations](#default-package-translations)
- [Upgrade](#upgrade)
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
    'format_custom_permission_keys' => true,
],
```

> **Separator & case compatibility:** The separator must not conflict with the case format's own delimiter. Using `_` with `snake`/`lower_snake`/`upper_snake`, or `-` with `kebab`, will throw an `InvalidArgumentException` since it would be impossible to distinguish the affix from the subject in the resulting permission key.

### Case
Shield formats permission keys using the specified case style. The available options are:
- `camel`
- `kebab` 
- `snake`
- `pascal` (default)
- `upper_snake`

### Customize permission key composition
You can customize how permission keys are generated by providing your own callback to `buildPermissionKeyUsing` in your `AppServiceProvider`'s `boot()` method. The callback receives the following parameters:
- `string $entity`: The FQCN of the entity for resources/pages/widgets, or `'custom'` for custom permissions.
- `?string $affix`: The action or method name (e.g., 'viewAny', 'create'). `null` for custom permissions.
- `string $subject`: The subject or resource name (e.g., 'Post', 'Dashboard'). For custom permissions, this is the raw permission key as defined in config.
- `string $case`: The case format specified in the config (e.g., 'pascal').
- `string $separator`: The separator specified in the config (e.g., ':').

Return a `string` to use as the permission key, or `null` to fall back to the default permission key builder. This allows you to selectively override specific entity types while keeping the default behavior for others:

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

* **Returning `null` for default fallback:** You can return `null` from the closure to let the default builder handle specific entity types. This is useful when you only want to customize certain entities (e.g., custom permissions from Keycloak) while letting everything else use the standard formatting:

  ```php
  use BezhanSalleh\FilamentShield\Facades\FilamentShield;

  FilamentShield::buildPermissionKeyUsing(
      function (string $entity, ?string $affix, string $subject, string $case, string $separator) {
          // Custom permissions from external source — use as-is
          if ($entity === 'custom') {
              return $subject;
          }

          // Everything else uses the default builder
          return null;
      }
  );
  ```

## Policies
Shield automatically generates policies for your Resources' Models.

### Configuration
```php
'policies' => [
    'path' => app_path('Policies'),
    'merge' => true,
    'generate' => true,
    'methods' => [
        'viewAny', 'view', 'create', 'update', 'delete', 'deleteAny', 'restore',
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

### Policy Placement
Shield writes each policy where it belongs for the model that owns it; how policies are resolved at runtime stays in your hands. The rule is applied per model:

1. **Models under `app/Models`** — the policy goes into `policies.path`, keeping any nesting (`app/Models/Blog/Post.php` → `app/Policies/Blog/PostPolicy.php`).
2. **Vendor models** — the policy goes flat into `policies.path`. Shield never writes inside `vendor/`, since Composer wipes it.
3. **Models in any other `Models` directory** (modules, plugins, DDD domains, panel-organized trees) — the policy goes into a sibling `Policies` directory beside the model, exactly where Laravel's policy discovery looks.
4. **Models outside any `Models` directory** (legacy `app/User.php` layouts) — the policy goes flat into `policies.path`.

| Model location | Generated policy | Found by Laravel's discovery? | Action needed |
|---|---|---|---|
| `app/Models/Post.php` (default `policies.path`) | `App\Policies\PostPolicy` | Yes | none |
| `app/Models/Blog/Post.php` | `App\Policies\Blog\PostPolicy` | No | `enforcePolicies()` or register |
| `app/Models/Post.php` (custom `policies.path`) | e.g. `App\Filament\Policies\PostPolicy` | No | `enforcePolicies()` or register |
| `app/Filament/Admin/Models/Post.php` | `App\Filament\Admin\Policies\PostPolicy` | Yes | none |
| `modules/Blog/src/Models/Post.php` | `Modules\Blog\Policies\PostPolicy` | Yes | none |
| `app/Domain/Users/Models/Post.php` | `App\Domain\Users\Policies\PostPolicy` | Yes | none |
| vendor model, no bundled policy | `App\Policies\PostPolicy` | No | `enforcePolicies()` or register (`register_role_policy` already covers Shield's `Role`) |
| `app/User.php` (no `Models` directory) | `App\Policies\UserPolicy` | Yes | none |

Because the rule is per-model, mixed layouts work with zero configuration: a default `app/Models` tree, an `app-modules/` directory, and vendor models can coexist in one app. Grouping models **inside** `app/Models` (e.g. `app/Models/Shared`, `app/Models/Admin`) mirrors the grouping into your policy tree under `policies.path`; grouping them **outside** it (e.g. `app/Filament/Admin/Models`) yields sibling placement that Laravel discovers on its own — the directory choice selects the trade-off.

### Skipping Provided Policies
When a model's policy already resolves to something other than the policy Shield would generate — for example a policy bundled with an installed plugin, or one you registered yourself — `shield:generate` skips that model and reports which policy provides it. Permissions are still generated.

Ownership is decided structurally, with two symmetric recipes and no flags:

- **Opting a model out** — put your policy anywhere you like, register it with `Gate::policy()`, and delete the file Shield generated. Shield treats it like a plugin-provided policy and backs off that model for good, while still generating its permissions.
- **Taking over a provided policy** — create a policy class at Shield's conventional location for the model (see the placement table above), for example with `php artisan make:policy`. Once that class exists, the next `shield:generate` fills it and maintains it from then on. Register it with `Gate::policy()` so it wins over the plugin's — explicit registrations beat discovered ones.

The `--ignore-existing-policies` flag is an unrelated axis: it prevents rewriting any policy file that already exists, protecting manual edits. The skip rule decides whether a model is Shield's to generate for; the flag then decides whether an existing file may be rewritten.

One caveat: the check runs in the console, so registrations that only happen conditionally at runtime may not be visible while generating. The worst case is an extra generated file that never resolves — Shield itself never registers anything without being asked.

### Methods
Each policy includes methods defined in the `policies.methods` config. You can customize this list to fit your application's needs. Since Filament Resources typically use a standard set of methods, the default configuration should suffice for most applications. If you have specific resources that require additional methods, you can easily add them to the list. 
However, it would be best to only include methods that are commonly used across your resources and define any resource-specific methods in the `resources.manage` config section. This approach keeps your policies clean and relevant to your application's requirements.

### Merge
When `policies.merge` is set to `true`, Shield will combine the global methods defined in `policies.methods` with any resource-specific methods you define in `resources.manage`. This ensures that each resource's policy includes both the standard methods and any additional ones you need for that particular resource.

### Single Parameter Methods
Some policy methods only require the user instance as a parameter (e.g., `viewAny`, `create`). These are defined in `policies.single_parameter_methods`. Shield will generate these methods accordingly in the policies. When you add new methods or resource-specific methods, ensure to update this list if they also only require the user instance. This helps maintain consistency and clarity in your policy definitions.

### Policy Enforcement
Sibling-placed policies and the default flat `App\Policies` are found by Laravel's policy discovery on their own. The placements marked "No" in the table above — nested under `policies.path`, a custom `policies.path`, and centralized vendor-model policies — are invisible to it and need registration. The simplest way is Shield's opt-in enforcement hook, in a service provider's `boot()` method:

```php
use BezhanSalleh\FilamentShield\Facades\FilamentShield;

FilamentShield::enforcePolicies();
```

At `Filament::serving` time, this registers the Shield-generated policy for each of your resources' models via `Gate::policy()`. It plays by strict rules:

- The condition is evaluated lazily on each request, so a closure can gate enforcement per panel or per tenant: `FilamentShield::enforcePolicies(fn (): bool => Filament::getCurrentPanel()?->getId() === 'admin')`.
- Models listed in `$except` are left alone: `FilamentShield::enforcePolicies(except: [Post::class])`.
- Existing explicit `Gate::policy()` registrations — yours or a plugin's — always win, regardless of boot order.
- Only Shield-generated policy classes that actually exist are registered; your custom-located policies are never touched, and nothing is ever unregistered or overridden.

For full manual control, register policies yourself:

```php
Gate::policy(Awcodes\Curator\Models\Media::class, App\Policies\MediaPolicy::class);
```

**Tip** Alternatively, you can teach Laravel's discovery your convention with `Gate::guessPolicyNamesUsing()`. If you use a custom `policies.path`, adapt the callback to your configured namespace, since discovery never looks inside a custom path on its own:

```php
use Illuminate\Support\Facades\Gate;

Gate::guessPolicyNamesUsing(function (string $modelClass) {
    return str_replace('Models', 'Policies', $modelClass) . 'Policy';
});
```

One boundary to be aware of: resolving *different* policies per panel for the same model is conditional resolution, which belongs in your own registration logic (a conditioned `enforcePolicies()` closure only gates whether Shield's policies are enforced — it does not swap policies per panel).

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

### Formatting
By default, custom permission keys are formatted according to the configured `case` setting. When a custom permission contains the configured `separator`, each segment is formatted independently. For example, with `case => 'snake'` and `separator => ':'`:
- `'view system log'` → `view_system_log`
- `'View:SystemLog'` → `view:system_log`

Shield's formatter is fault-tolerant — it normalizes input regardless of the original format (snake_case, kebab-case, camelCase, PascalCase, UPPER_SNAKE_CASE) before applying the target case conversion.

### Externally Managed Permissions
If your custom permissions come from external sources like **Terraform**, **Keycloak**, or other identity providers and must retain their exact key names, set `format_custom_permission_keys` to `false`:
```php
'permissions' => [
    'separator' => ':',
    'case' => 'pascal',
    'generate' => true,
    'format_custom_permission_keys' => false,
],
```
With this setting, custom permission keys are stored exactly as defined — no case conversion is applied. This does not affect resource, page, or widget permissions, which are always formatted.

Alternatively, you can use the `buildPermissionKeyUsing` closure for more granular control — see [Customize permission key composition](#customize-permission-key-composition).

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
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
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

shield:translation {locale} [--panel=] [--path=]
```

### Generate Command Options (recap)

```bash
--all  Generate for all discovered entities
--option=policies_and_permissions|policies|permissions|tenant_relationships Override generator mode
--resource=FooResource,BarResource  Target resources (class basenames)
--page=Dashboard,Settings           Target pages (basenames)
--widget=StatsOverview,SalesChart   Target widgets (basenames)
--exclude                           Treat provided entities as exclusions
--ignore-existing-policies          Skip policies whose file already exists, preserving manual edits
--panel=admin                       Panel ID (required when not interactive)
--relationships                     Generate tenancy relationships (panel must have tenancy)
```

### Seeder Command Options (recap)

```bash
--generate    Generate seeder file
--option=permissions_via_roles|direct_permissions   Choose seeder type
--with-users    Export users based on their roles/permissions
--all   Export all tenants/users regardless of role assignments
--include-passwords Include existing hashed passwords from database
--generate-passwords=   Generate passwords (random, prompt, or custom value)
--force   Overwrite existing seeder file
```

### Custom Super Admin Creation

When no user exists yet, `shield:super-admin` prompts for a name, email, and password. If your `User` model requires more than that, register a closure via `SuperAdminCommand::createSuperAdminUsing()` in a service provider's `boot()` method and the command will call it instead:

```php
use BezhanSalleh\FilamentShield\Commands\SuperAdminCommand;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

public function boot(): void
{
    SuperAdminCommand::createSuperAdminUsing(function (): ?Authenticatable {
        return \App\Models\User::create([
            'name' => text(label: 'First Name', required: true),
            'last_name' => text(label: 'Last Name', required: true),
            'email' => text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    \App\Models\User::where('email', $email)->exists() => 'A user with this email address already exists.',
                    default => null,
                },
            ),
            'password' => Hash::make(password(
                label: 'Password',
                required: true,
                validate: fn (string $value): ?string => match (true) {
                    strlen($value) < 8 => 'The password must be at least 8 characters.',
                    default => null,
                },
            )),
        ]);
    });

    SuperAdminCommand::prohibit($this->app->isProduction());
}
```

The closure is resolved through the container and should return an `Authenticatable` instance. Returning `null` deliberately falls back to the built-in interactive prompts, so a closure can hand control back whenever it decides not to create the user itself.

Keep in mind that the closure replaces Shield's built-in email and password validation entirely — validate whatever you collect, and never hardcode credentials in it. This hook is meant for bootstrapping development and staging environments only; in production, create the super admin through a seeder or a deliberate one-time run, and pair the hook with `SuperAdminCommand::prohibit($this->app->isProduction())` in the same `boot()` as shown above.

## Localization
Shield supports multiple languages out of the box. When enabled, you can provide translated labels for
permissions to create a more localized experience for your app's users.

### Configuration
```php
'localization' => [
     'enabled' => false,
     'key' => 'shield-permissions', // could be any name you want
 ],
```

### How It Works

Shield uses a **fallback chain** for resolving permission labels:

1. **User's translation file** (when `localization.enabled = true`)
   - Checks `lang/{locale}/{key}.php` where `{key}` is your configured localization key
2. **Package's default translations**
   - Falls back to `resource_permission_prefixes_labels` for standard affixes (view, create, update, etc.)
3. **Headline fallback**
   - Converts the key to a readable format (e.g., `force_delete_any` → "Force Delete Any")

### Generating Translation Files

The easiest way to create a translation file is using the `shield:translation` command:

```bash
php artisan shield:translation en --panel=admin
```

This generates a file at `lang/en/shield-permissions.php` containing all permission labels:

```php
<?php

/**
 * Shield Permission Labels
 *
 * Translate the values below to localize permission labels in your application.
 */

return [
    // Resource affixes
    'create' => 'Create',
    'delete' => 'Delete',
    'delete_any' => 'Delete Any',
    'force_delete' => 'Force Delete',
    'force_delete_any' => 'Force Delete Any',
    'replicate' => 'Replicate',
    'reorder' => 'Reorder',
    'restore' => 'Restore',
    'restore_any' => 'Restore Any',
    'update' => 'Update',
    'view' => 'View',
    'view_any' => 'View Any',

    // Pages (permission key in snake_case)
    'view_dashboard' => 'Dashboard',

    // Widgets (permission key in snake_case)
    'view_stats_overview' => 'Stats Overview',

    // Custom permissions
    'approve_posts' => 'Approve Posts',
];
```

### Translation Keys

All translation keys are in **snake_case** format:

| Permission Type | Original Key | Translation Key |
|-----------------|--------------|-----------------|
| Resource affix | `viewAny` | `view_any` |
| Resource affix | `forceDeleteAny` | `force_delete_any` |
| Page permission | `view:Dashboard` | `view_dashboard` |
| Widget permission | `view:StatsOverview` | `view_stats_overview` |
| Custom permission | `Approve:Posts` | `approve_posts` |

### Default Package Translations

Shield includes translations for standard resource affixes in 32 languages. When `localization.enabled = false`,
the package automatically uses these translations for affixes like `view`, `create`, `update`, `delete`, etc.

For entity labels (Resources, Pages, Widgets), Filament's entity related methods are used
(`getModelLabel()`, `getTitle()`, `getHeading()`, etc.).


# Upgrade
Upgrading from `3.x|4.0.0-Beta*` versions to 4.x requires careful consideration due to significant changes in the package's architecture and functionality. Here are the key steps and considerations for a successful upgrade:
1. **Backup Your Data**: Before making any changes, ensure you have a complete backup of your database and application files. This is crucial in case you need to revert to the previous version.
2. **Remove Config and Resource**: Delete the existing `filament-shield.php` config file and the published `RoleResource` if you have done so. This is important to avoid conflicts with the new configuration and resource structure.
3. **Update Composer**: Run `composer require bezhansalleh/filament-shield` to update the package to the latest version.
4. **Publish New Config and Resource**: Publish the new configuration file and the `RoleResource` using the following commands:
   ```bash
   php artisan vendor:publish --tag="filament-shield-config"
   php artisan shield:publish --panel=admin # you can ignore this if you didn't published the resource previously
   ```
5. **Adjust Configuration**: Review and adjust the new `filament-shield.php` configuration file to match your application's requirements. Pay special attention to the new options and defaults that may differ from the previous version.
6. **HasShieldPermissions Contract is Deprecated**: If you have implemented the `HasShieldPermissions` contract in your resources, consult [Policies](#policies) and [Resources](#resources) sections on how to migrate. If you leave it as is, it will be ignored.
7. **Clean Slate** or **Perserve**: Decide whether to start fresh with a clean slate or preserve existing roles and permissions.
    1. **Clean Slate**: If you choose to start fresh, you can run the following command to and follow along to set up the package from scratch.
         ```bash
         php artisan shield:setup --fresh
         ```
    2. **Preserve Existing Data**: If you want to keep your existing roles and permissions intact, then follow these steps:
        1. Add the following code to your `AppServiceProvider`'s `boot()` method to perserve the the previous versions(3.x|4.x-Beta*) permission pattern:
            ```php
            use BezhanSalleh\FilamentShield\Facades\FilamentShield;
            use Filament\Pages\BasePage as Page;
            use Filament\Resources\Resource;
            use Filament\Widgets\Widget;
            use Illuminate\Support\Str;

            //...
            public function boot(): void
            {
                FilamentShield::buildPermissionKeyUsing(
                        function (string $entity, string $affix, string $subject, string $case, string $separator) {
                            return match(true) {
                                # if `configurePermissionIdentifierUsing()` was used previously, then this needs to be adjusted accordingly
                                is_subclass_of($entity, Resource::class) => Str::of($affix)
                                    ->snake()
                                    ->append('_')
                                    ->append(
                                        Str::of($entity)
                                            ->afterLast('\\')
                                            ->beforeLast('Resource')
                                            ->replace('\\', '')
                                            ->snake()
                                            ->replace('_', '::')
                                    )
                                    ->toString(),
                                is_subclass_of($entity, Page::class) => Str::of('page_')
                                    ->append(class_basename($entity))
                                    ->toString(),
                                is_subclass_of($entity, Widget::class) => Str::of('widget_')
                                    ->append(class_basename($entity))
                                    ->toString()
                                };
                        });
            }
            ```
        2. If you have used the `configurePermissionIdentifierUsing()` method to customize the permission key composition, then adjust the logic for resources above to match your custom logic.
        3. Running the `shield:generate` command 
           - If your policies are altered or customized, you may need to run the generate command carefully per resource or set of resources to avoid any unwanted side effects. Then manually review and adjust the customized policies as needed. :
               ```bash
               php artisan shield:generate --resource=FooResource,BarResource --option=policies
               ```
            - If you haven't customized your policies then run the following command to ensure that your policies are up to date with the latest version of Shield:
               ```bash
               php artisan shield:generate --all --option=policies
               ```
           - If you have tenancy enabled in your panels and you want to generate the tenancy relationships, you can add the `--relationships` flag to the above commands.
        4. Review and adjust the generated policies and permissions as needed.
        
8. **Test Thoroughly**: After completing the upgrade, thoroughly test your application to ensure that all functionalities related to roles, permissions, and access control are working as expected. Pay special attention to any custom implementations you may have had in place.


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
