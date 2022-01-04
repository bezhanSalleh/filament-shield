<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://user-images.githubusercontent.com/10007504/148017180-a638248e-ba9a-4278-a099-1f8c1cbf068f.png" >

# Filament Shield

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bezhansalleh/filament-shield.svg?style=flat-square)](https://packagist.org/packages/bezhansalleh/filament-shield)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/run-tests?label=tests)](https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bezhansalleh/filament-shield.svg?style=flat-square)](https://packagist.org/packages/bezhansalleh/filament-shield)
#### The easiest and most intuitive way to add access management to your Filament Resources (more coming soon 😎)
One Plugin to rule them all, One Plugin to find them, One Plugin to bring them all, and in the light bind them, In the Land of Filament where building them is really fun!

## Support Filament

<a href="https://github.com/sponsors/danharrin">
<img width="320" alt="filament-logo" src="https://filamentadmin.com/images/sponsor-banner.jpg">
</a>

## Installation (New Apps)

1. Install the package via composer:

```bash
composer require bezhansalleh/filament-shield
```

2. Publish the config file with:

```bash
php artisan vendor:publish --tag="filament-shield-config"
```

3. Change `super_admin_role_name` if you want. (You can skip this step if you use the default)
Every `permission` generated will be automatically assigned to this role.
Then you would be able to make a `user` super admin by assigning this role to them.

```php
'default_roles' => [
     'super_admin_role_name' => 'super_admin',
     'filament_user' => [
         'role_name' => 'filament_user',
         'enabled' => true
     ],
 ],
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
#### Apps with `spatie/laravel-permission` already installed

5. Follow the above steps from (1) thorugh (4) run the following command to setup everything:

```bash
php artisan shield:install --fresh
```

Beaware, that even though there are checks in place but if confirmed; existing policies might get overwritten.

### `RolePolicy`
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

## Available Filament Shield Commands

```bash
  shield:create    # Create Permissions and/or Policy for the given Filament Resource Model
  shield:generate  # (Re)Discovers Filament resources and (re)generates Permissions and Policies.
  shield:install   # One Command to Rule them All 🔥
  shield:publish   # Publish filament shield's Resource.
```

## Chores

- [x] `shield:install` command flow improved ⚒️
- [ ] Improve documentation 📝
- [ ] A command to reverse everything 🤯
- [ ] handle `except` or `only` options for Permission and Policy generation 👀
- [ ] improve automation or add new features... ⏭

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
