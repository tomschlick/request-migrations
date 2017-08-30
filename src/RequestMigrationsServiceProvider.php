<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Http\Kernel;
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
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware(\TomSchlick\RequestMigrations\RequestMigrationsMiddleware::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'request-migrations');

        $this->commands([RequestMigrationMakeCommand::class]);

        $this->app->bind('getRequestMigrationsVersions', function() {

            $migrationsPath = app_path('Http/Migrations');

            if(File::exists($migrationsPath)) {
                return collect(File::directories($migrationsPath))->map(function($versionDirectory) {
                    return substr($versionDirectory, strpos($versionDirectory, 'Version_') + 8);
                });
            }

            return collect();
        });
    }
}
