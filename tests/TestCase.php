<?php

namespace TomSchlick\RequestMigrations\Tests;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use TomSchlick\RequestMigrations\RequestMigrationsMiddleware;
use TomSchlick\RequestMigrations\RequestMigrationsServiceProvider;
use TomSchlick\RequestMigrations\Tests\Migrations\GroupNameMigration;
use TomSchlick\RequestMigrations\Tests\Migrations\TitleMigration;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
        $this->setupConfig($this->app);
        $this->setUpRoutes($this->app);
        $this->setUpMiddleware();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RequestMigrationsServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [];
    }

    protected function setupConfig($app)
    {
        $app['config']->set('request-migrations', [
            'headers' => [
                'current-version'  => 'x-api-current-version',
                'request-version'  => 'x-api-request-version',
                'response-version' => 'x-api-response-version',
            ],

            'current_version' => '2017-04-04',

            'versions' => [
                '2017-01-01' => [],
                '2017-02-02' => [],
                '2017-03-03' => [
                    GroupNameMigration::class,
                ],
                '2017-04-04' => [
                    TitleMigration::class,
                ],
            ],
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpRoutes($app)
    {
        Route::get('users/show', function () {
            return [
                'id'     => 123,
                'name'   => [
                    'firstname' => 'Dwight',
                    'lastname'  => 'Schrute',
                ],
                'title'  => 'Assistant to the Regional Manager',
                'skills' => [
                    'bears',
                    'beats',
                    'battlestar galactica',
                ],
            ];
        });

        Route::post('users', function () {
            return [
                'id'        => 456,
                'firstname' => request('firstname'),
                'lastname'  => request('lastname'),
                'title'     => request('title'),
                'skills'    => request('skills'),
            ];
        });

        $app['router']->getRoutes()->refreshNameLookups();
    }

    protected function setUpMiddleware()
    {
        $this->app[Kernel::class]->pushMiddleware(RequestMigrationsMiddleware::class);
    }
}
