<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class Migrator
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Http\Response
     */
    protected $response;

    protected $currentVersion = null;

    protected $requestVersion = null;

    protected $responseVersion = null;

    /**
     * @var array
     */
    protected $config;

    /**
     * Migrator constructor.
     *
     * @param \Illuminate\Http\Request $request
     * @param array                    $config
     */
    public function __construct(Request $request, array $config)
    {
        $this->request = $request;
        $this->config = $config;

        $this->currentVersion = Arr::get($config, 'current_version');
        $this->requestVersion = $request->header(Arr::get($config, 'headers.request-version'));
        $this->responseVersion = $request->header(Arr::get($config, 'headers.response-version'));
    }

    /**
     * Process the migrations for the incoming request.
     *
     * @return \Illuminate\Http\Request
     */
    public function processRequestMigrations() : Request
    {
        Collection::make($this->neededMigrations($this->requestVersion))
            ->transform(function ($migrations) {
                return Collection::make($migrations)->flatten();
            })
            ->flatten()
            ->each(function ($migration) {
                $this->request = (new $migration())->migrateRequest($this->request);
            });

        return $this->request;
    }

    /**
     * Process the migrations for the outgoing response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return $this
     */
    public function processResponseMigrations(Response $response)
    {
        $this->response = $response;

        Collection::make($this->neededMigrations($this->responseVersion))
            ->reverse()
            ->transform(function ($migrations) {
                return Collection::make($migrations);
            })
            ->flatten()
            ->each(function ($migration) {
                $this->response = (new $migration())->migrateResponse($this->response);
            });

        return $this;
    }

    /**
     * Set the API Response Headers.
     *
     * @return $this
     */
    public function setResponseHeaders()
    {
        $this->response->headers->set(Arr::get($this->config, 'headers.current-version'), $this->currentVersion, true);
        $this->response->headers->set(Arr::get($this->config, 'headers.request-version'), $this->requestVersion, true);
        $this->response->headers->set(Arr::get($this->config, 'headers.response-version'), $this->responseVersion, true);

        return $this;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Figure out which migrations apply to the current request.
     *
     * @param $migrationVersion string The migration version to check migrations against
     *
     * @return array
     */
    public function neededMigrations($migrationVersion) : array
    {
        return Collection::make(
                app()->make('getRequestMigrationsVersions')
            )
            ->reject(function ($classList, $version) use ($migrationVersion) {
                return $version <= $migrationVersion;
            })
            ->filter(function ($classList) {
                return Collection::make($classList)->transform(function ($class) {
                    return Collection::make((new $class)->paths())->filter(function ($path) {
                        return $this->request->fullUrlIs($path);
                    });
                })->isNotEmpty();
            })->toArray();
    }
}
