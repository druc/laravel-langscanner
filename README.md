# laravel-langscanner

[![Latest Version on Packagist](https://img.shields.io/packagist/v/druc/laravel-langscanner.svg?style=flat-square)](https://packagist.org/packages/druc/laravel-langscanner)
[![Tests](https://github.com/druc/laravel-langscanner/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/druc/laravel-langscanner/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/druc/laravel-langscanner.svg?style=flat-square)](https://packagist.org/packages/druc/laravel-langscanner)

This package scans your project for missing translation keys and then writes them into individual json files for you to fill in.

## Installation

You can install the package via composer:

```bash
composer require druc/laravel-langscanner
```

## Usage

Scan your project for missing translations:

```
// outputs and writes translations for the specified language (dutch)
php artisan langscanner nl

// outputs and writes translations in the existing {language}.json files
php artisan langscanner
```

## Credits

This package is based on [joedixon/laravel-translation](https://github.com/joedixon/laravel-translation) and [themsaid/laravel-langman-gui](https://github.com/themsaid/laravel-langman-gui)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
