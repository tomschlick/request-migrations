<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
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
        $this->app->singleton(Migrator::class, function () {
            return new Migrator(Config::get('request-migrations'));
        });

        $this->app->alias(Migrator::class, 'request-migrations');

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'request-migrations');

        $this->commands([RequestMigrationMakeCommand::class]);
    }
}
