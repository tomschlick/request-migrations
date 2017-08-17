<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * Migrator constructor.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->currentVersion = config('request-migrations.current_version');
        $this->requestVersion = $request->header(config('request-migrations.headers.request-version'));
        $this->responseVersion = $request->header(config('request-migrations.headers.request-version'));
    }

    /**
     * Process the migrations for the incoming request.
     *
     * @return \Illuminate\Http\Request
     */
    public function processRequestMigrations() : Request
    {
        foreach (array_reverse($this->neededMigrations()) as $version => $migrations) {
            foreach ($migrations as $migration) {
                $this->request = (new $migration())->migrateRequest($this->request);
            }
        }

        return $this->request;
    }

    /**
     * Process the migrations for the outgoing response.
     *
     * @param \Illuminate\Http\Response $response
     */
    public function processResponseMigrations(Response $response)
    {
        $this->response = $response;

        foreach ($this->neededMigrations() as $version => $migrations) {
            foreach ($migrations as $migration) {
                $this->response = (new $migration())->migrateResponse($this->response);
            }
        }
    }

    /**
     * Set the API Response Headers.
     */
    public function setResponseHeaders()
    {
        $this->response->headers->set(config('request-migrations.headers.current-version'), $this->currentVersion, true);
        $this->response->headers->set(config('request-migrations.headers.request-version'), $this->requestVersion, true);
        $this->response->headers->set(config('request-migrations.headers.response-version'), $this->responseVersion, true);
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
     * @return array
     */
    public function neededMigrations() : array
    {
        $needed = [];

        // TODO: Refactor this to use collections
        foreach (config('request-migrations.versions') as $version => $classList) {
            $items = [];

            if ($version <= $this->requestVersion) {
                continue;
            }

            foreach ($classList as $class) {
                $migration = (new $class());
                foreach ($migration->paths() as $path) {
                    if ($this->request->is($path)) {
                        $items[] = $class;
                    }
                }
            }

            if (count($items)) {
                $needed[$version] = $items;
            }
        }

        return $needed;
    }
}
