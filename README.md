# Filament support for `spatie/laravel-permission`.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bezhansalleh/filament-shield.svg?style=flat-square)](https://packagist.org/packages/bezhansalleh/filament-shield)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/run-tests?label=tests)](https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bezhansalleh/filament-shield/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bezhansalleh/filament-shield/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bezhansalleh/filament-shield.svg?style=flat-square)](https://packagist.org/packages/bezhansalleh/filament-shield)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

## Installation

You can install the package via composer:

```bash
composer require bezhansalleh/filament-shield
```


You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-shield-config"
```

Optionally, you can publish the translations using

```bash
php artisan vendor:publish --tag="filament-shield-translations"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filament-shield = new BezhanSalleh\FilamentShield();
echo $filament-shield->echoPhrase('Hello, BezhanSalleh!');
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
