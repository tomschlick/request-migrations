# HTTP Request Migrations

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tomschlick/request-migrations.svg?style=flat-square)](https://packagist.org/packages/tomschlick/request-migrations)
[![Build Status](https://img.shields.io/travis/tomschlick/request-migrations/master.svg?style=flat-square)](https://travis-ci.org/tomschlick/request-migrations)

This package is based on the [API versioning scheme used at Stripe](https://stripe.com/blog/api-versioning). Basically, instead of using /v1, /v2, etc in your urls; Users pass a version header and you automatically migrate the request & response data to match the current version of your code.

## Installation

You can install the package via composer:

### Installation via Composer

```bash
composer require tomschlick/request-migrations
```
### Service Provider

This package supports Laravel 5.5 autoloading. If you are using an earlier version of Laravel or have autoloading disabled you need to add the service provider to `config/app.php`.

```php
\TomSchlick\RequestMigrations\RequestMigrationsMiddleware::class,
```

### Middleware

Add the middleware to your Http Kernel `app/Http/Kernel.php`.

```php
protected $middleware = [
	\TomSchlick\RequestMigrations\RequestMigrationsMiddleware::class,
];

```

### Configuration

Run the following Artisan command to publish the package configuration to `config/request-migrations.php`.

```bash
php artisan vendor:publish --provider="TomSchlick\RequestMigrations\RequestMigrationsServiceProvider"
```

## Usage

```php

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tom@schlick.email instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
