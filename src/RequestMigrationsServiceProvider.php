<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Support\ServiceProvider;
use TomSchlick\RequestMigrations\Commands\RequestMigrationMakeCommand;

class RequestMigrationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('request-migrations.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'request-migrations');

        $this->app->singleton('command.request-migration.make', function ($app) {
            return new RequestMigrationMakeCommand($app['files']);
        });

        $this->commands([
            'command.request-migration.make',
        ]);
    }
}
