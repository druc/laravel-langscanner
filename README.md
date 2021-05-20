# laravel-langscanner

[![Latest Version on Packagist](https://img.shields.io/packagist/v/druc/laravel-langscanner.svg?style=flat-square)](https://packagist.org/packages/druc/laravel-langscanner)
[![Total Downloads](https://img.shields.io/packagist/dt/druc/laravel-langscanner.svg?style=flat-square)](https://packagist.org/packages/druc/laravel-langscanner)

This package scans your project for missing translation keys and then writes them into individual json files for you to fill in.

## Usage

Scan your project for missing translations:

```
php artisan langscanner
// outputs the translations
// writes them to translation files: en.json. nl.json, de.json, etc.
```

## Installation

You can install the package via composer:

```bash
composer require druc/laravel-langscanner
```

## Credits

This package is based on [joedixon/laravel-translation](https://github.com/joedixon/laravel-translation) and [themsaid/laravel-langman-gui](https://github.com/themsaid/laravel-langman-gui)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
