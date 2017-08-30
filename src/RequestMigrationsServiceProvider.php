<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use TomSchlick\RequestMigrations\Commands\CacheRequestMigrationsCommand;
use TomSchlick\RequestMigrations\Commands\RequestMigrationMakeCommand;

class RequestMigrationsServiceProvider extends ServiceProvider
{
    const REQUEST_MIGRATIONS_CACHE = '/bootstrap/cache/request-migrations.php';

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

        if (!$this->migrationHasAlreadyBeenPublished()) {
            $this->publishes([
                __DIR__.'/database/migrations/add_api_version_to_users_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_add_api_version_to_users_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'request-migrations');

        $this->commands([
            RequestMigrationMakeCommand::class,
            CacheRequestMigrationsCommand::class,
        ]);

        $this->app->bind('getRequestMigrationsVersions', function() {

            $cacheFile = base_path(self::REQUEST_MIGRATIONS_CACHE);

            if(File::exists($cacheFile)) {
                return collect(require($cacheFile));
            }

            return $this->generateRequestMigrations();

        });
    }

    /**
     * @return \Illuminate\Support\Collection|static
     */
    protected function generateRequestMigrations()
    {
        $migrationsPath = app_path('Http/Migrations');

        // We generate the class list dynamically instead of relying on the config
        if(File::exists($migrationsPath)) {

            return collect(File::directories($migrationsPath))
                ->map(function($versionDirectory) {
                    return substr($versionDirectory, strpos($versionDirectory, 'Version_') + 8);
                })
                ->flip()
                ->map(function($value, $key) use($migrationsPath) {

                    $files = collect();

                    foreach(File::files($migrationsPath.'/Version_'.$key) as $file) {
                        $files->push(app()->getNamespace().'Http\Migrations\Version_'.$key.'\\'.$file->getBasename('.php'));
                    }

                    return $files;

                });
        }

        return collect();
    }

    /**
     * Checks to see if the migration has already been published.
     *
     * @return bool
     */
    protected function migrationHasAlreadyBeenPublished()
    {
        return count(
            glob(
                database_path('migrations/*_add_api_version_to_users_table.php')
            )
        ) > 0;
    }
}
