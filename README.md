# HTTP Request Migrations

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tomschlick/request-migrations.svg?style=flat-square)](https://packagist.org/packages/tomschlick/request-migrations)
[![Build Status](https://img.shields.io/travis/tomschlick/request-migrations/master.svg?style=flat-square)](https://travis-ci.org/tomschlick/request-migrations)
[![Total Downloads](https://poser.pugx.org/tomschlick/request-migrations/downloads)](https://packagist.org/packages/tomschlick/request-migrations)
[![StyleCI](https://styleci.io/repos/100408108/shield)](https://styleci.io/repos/100408108)

This package is based on the [API versioning scheme used at Stripe](https://stripe.com/blog/api-versioning). Users pass a version header and you automatically migrate the request & response data to match the current version of your code.

## Installation

You can install the package via composer:

### Installation via Composer

```bash
composer require tomschlick/request-migrations
```
### Service Provider & Facade

This package supports Laravel 5.5 autoloading so the service provider and facade will be loaded automatically. 

If you are using an earlier version of Laravel or have autoloading disabled you need to add the service provider and facade to `config/app.php`.

```php
'providers' => [
    \TomSchlick\RequestMigrations\RequestMigrationsServiceProvider.php,
]
```
```php
'aliases' => [
    'RequestMigrations' => \TomSchlick\RequestMigrations\Facades\RequestMigrations::class,
]
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

### Creating a Migration

You can generate a new request migration using the Artisan CLI.

```shell
php artisan make:request-migration ExampleMigration

```

The command will generate a request migration and publish it to `App/Http/Migrations/*`.

It will generate a migration, you can modify it like this:

```php
class GroupNameMigration extends RequestMigration
{
    /**
     * Migrate the request for the application to "read".
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Request
     */
    public function migrateRequest(Request $request) : Request
    {
        return $request;
    }

    /**
     * Migrate the response to display to the client.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function migrateResponse(Response $response) : Response
    {
        $content = json_decode($response->getContent(), true);

        $content['firstname'] = array_get($content, 'name.firstname');
        $content['lastname'] = array_get($content, 'name.lastname');
        unset($content['name']);

        return $response->setContent(json_encode($content));
    }

    /**
     * Define which named paths should this migration modify.
     *
     * @return array
     */
    public function paths() : array
    {
        return [
            'users/show',
        ];
    }
}
```

### Override the Versions

```php
use TomSchlick\RequestMigrations\Facades\RequestMigrations;

// set both response & request versions
RequestMigrations::setVersion('2017-01-01')

// set the request version
RequestMigrations::setRequestVersion('2017-01-01')

// set the response version
RequestMigrations::setResponseVersion('2017-01-01')
```

This can be useful if you are pinning the version to a user.

```php
RequestMigrations::setVersion(auth()->user()->api_version);
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
