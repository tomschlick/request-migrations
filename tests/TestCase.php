<?php

namespace TomSchlick\RequestMigrations\Tests;

use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use TomSchlick\RequestMigrations\RequestMigrationsMiddleware;
use TomSchlick\RequestMigrations\Tests\Migrations\TitleMigration;
use TomSchlick\RequestMigrations\RequestMigrationsServiceProvider;
use TomSchlick\RequestMigrations\Tests\Migrations\PostBodyMigration;
use TomSchlick\RequestMigrations\Tests\Migrations\GroupNameMigration;
use TomSchlick\RequestMigrations\Tests\Migrations\PostTitleMigration;

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
                '2017-02-02' => [
                    PostBodyMigration::class,
                ],
                '2017-03-03' => [
                    GroupNameMigration::class,
                    PostTitleMigration::class,
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
        Route::get('users/show', function (Request $request) {
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
                'request' => $request->all(),
            ];
        });

        Route::post('users', function (Request $request) {
            return [
                'id'        => 456,
                'firstname' => request('firstname'),
                'lastname'  => request('lastname'),
                'title'     => request('title'),
                'skills'    => request('skills'),
                'request' => $request->all(),
            ];
        });

        Route::post('posts', function (Request $request) {
            return ['request' => $request->all()];
        });

        $app['router']->getRoutes()->refreshNameLookups();
    }

    protected function setUpMiddleware()
    {
        $this->app[Kernel::class]->pushMiddleware(RequestMigrationsMiddleware::class);
    }

    protected function assertMigrationEventsFired()
    {
        Event::assertDispatched([
            RequestIsMigrating::class,
            RequestHasMigrated::class,
            ResponseIsMigrating::class,
            ResponseHasMigrated::class,
        ]);
    }

    protected function assertMigrationEventsDidntFire()
    {
        Event::assertNotDispatched([
            RequestIsMigrating::class,
            RequestHasMigrated::class,
            ResponseIsMigrating::class,
            ResponseHasMigrated::class,
        ]);
    }
}
