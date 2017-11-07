<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use TomSchlick\RequestMigrations\Events\RequestIsMigrating;
use TomSchlick\RequestMigrations\Events\ResponseHasMigrated;
use TomSchlick\RequestMigrations\Events\ResponseIsMigrating;

class Migrator
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
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
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->currentVersion = Arr::get($config, 'current_version');
    }

    /**
     * Set the request and the versions from the request headers.
     *
     * @param Request $request
     * @return Migrator
     */
    public function setRequest(Request $request) : self
    {
        $this->request = $request;
        $this->requestVersion = $this->requestVersion ?: $request->header(Arr::get($this->config, 'headers.request-version'));
        $this->responseVersion = $this->responseVersion ?: $request->header(Arr::get($this->config, 'headers.response-version'));

        return $this;
    }

    /**
     * Set the response version.
     *
     * @param string $version
     * @return Migrator
     */
    public function setResponseVersion(string $version) : self
    {
        $this->responseVersion = $version;

        return $this;
    }

    /**
     * Set the request version.
     *
     * @param string $version
     * @return Migrator
     */
    public function setRequestVersion(string $version) : self
    {
        $this->requestVersion = $version;

        return $this;
    }

    /**
     * Set both the response and request version.
     *
     * @param string $version
     * @return Migrator
     */
    public function setVersion(string $version) : self
    {
        $this->requestVersion = $version;
        $this->responseVersion = $version;

        return $this;
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
                $class = new $migration();
                $originalRequest = $this->request;

                Event::fire(new RequestIsMigrating($class, $originalRequest));

                $this->request = $class->migrateRequest($originalRequest);

                Event::fire(new RequestIsMigrating($class, $originalRequest, $this->request));
            });

        return $this->request;
    }

    /**
     * Process the migrations for the outgoing response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
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
                $class = new $migration();
                $originalResponse = $this->response;

                Event::fire(new ResponseIsMigrating($class, $originalResponse));

                $this->response = $class->migrateResponse($originalResponse);

                Event::fire(new ResponseHasMigrated($class, $originalResponse, $this->response));
            });
    }

    /**
     * Set the API Response Headers.
     */
    public function setResponseHeaders()
    {
        $this->response->headers->set(Arr::get($this->config, 'headers.current-version'), $this->currentVersion, true);
        $this->response->headers->set(Arr::get($this->config, 'headers.request-version'), $this->requestVersion, true);
        $this->response->headers->set(Arr::get($this->config, 'headers.response-version'), $this->responseVersion, true);
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
     * @param $migrationVersion The migration version to check migrations against
     *
     * @return array
     */
    public function neededMigrations($migrationVersion) : array
    {
        return Collection::make(Arr::get($this->config, 'versions', []))
            ->filter(function ($classList, $version) use ($migrationVersion) {
                return $migrationVersion < $version;
            })->filter(function ($classList) {
                return $this->hasMigrationForCurrentPath($classList);
            })->toArray();
    }

    /**
     * Checks to see if any migrations should be applied to the current request route.
     *
     * @param array $migrationClasses
     *
     * @return bool
     */
    private function hasMigrationForCurrentPath(array $migrationClasses) : bool
    {
        return Collection::make($migrationClasses)->transform(function ($migrationClass) {
            return Collection::make((new $migrationClass)->paths())->filter(function ($path) {
                return $this->request->is($path);
            });
        })->isNotEmpty();
    }
}
