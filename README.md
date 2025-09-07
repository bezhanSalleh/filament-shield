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

## Table of Contents


<div class="filament-hidden">
<b>Table of Contents</b>

- [Features](#features)
- [Installation](#installation)
    - [1. Install Package](#1-install-package)
    - [2. Configure Auth Provider](#2-configure-auth-provider)
    - [3. Setup Shield](#3-setup-shield)
    - [4. Install for Panel](#4-install-for-panel)
- [Usage](#usage)
    - [Configuration](#configuration)
    - [Resources](#resources)
        - [Default](#default)
        - [Resource-Specific Methods](#resource-specific-methods)
        - [Custom Permissions](#custom-permissions)
        - [Custom Navigation Group](#custom-navigation-group)
    - [Pages](#pages)
    - [Widgets](#widgets)
    - [Policies](#policies)
    - [Users (Assigning Roles to Users)](#users-assigning-roles-to-users)
    - [Layout Customization](#layout-customization)
- [Available Commands](#available-commands)
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

Customize permission key composition using:
```php
use BezhanSalleh\FilamentShield\Facades\FilamentShield;

FilamentShield::buildPermissionKeyUsing(function (string $entity, string $affix, string $subject, string $case, string $separator) {
    return str($affix)->kebab() . '.' . str($subject)->kebab();
});
```

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
