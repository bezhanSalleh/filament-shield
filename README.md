<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://user-images.githubusercontent.com/10007504/147907752-dbf4a109-94ab-4d9d-b5da-9ef8db223806.png" >

# Filament Shield

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bezhansalleh/filament-shield.svg?style=flat-square)](https://packagist.org/packages/bezhansalleh/filament-shield)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/run-tests?label=tests)](https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bezhansalleh/filament-shield.svg?style=flat-square)](https://packagist.org/packages/bezhansalleh/filament-shield)

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

3. Configure `super_admin` role and change the `role_name` to something of your choosing. 
It is recommend to leave it enable this way every `permission` generated will be automatically assigned to this role.
then you would only need to assign this role to your super admin `user`.

```php
'super_admin' => [
    'enabled' => true,
    'role_name' => 'super_admin',
],
```

4. Now run the following command to setup everything:

```bash
php artisan shield:install

```
Follow the prompts and enjoy!

## Installation (Existing Apps)

4. For existing apps run the following command to setup everything:

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

- [ ] Imporve documentation 📝
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
