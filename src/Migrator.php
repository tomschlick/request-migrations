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

    protected $currentVersion = '';

    protected $requestVersion = '123';

    protected $responseVersion = '123';

    /**
     * Migrator constructor.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Process the migrations for the incoming request.
     *
     * @return \Illuminate\Http\Request
     */
    public function processRequestMigrations() : Request
    {
        foreach ($this->neededMigrations() as $migration) {
            $this->request = (new $migration())->migrateRequest($this->request);
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

        foreach ($this->neededMigrations() as $migration) {
            $this->response = (new $migration())->migrateResponse($this->response);
        }
    }

    /**
     * Set the API Response Headers
     */
    public function setResponseHeaders()
    {
        $this->response->headers->set('x-api-request-version', $this->requestVersion, false);
        $this->response->headers->set('x-api-response-version', $this->responseVersion, false);
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
        $routeName = $this->request->route()->getName();

        // TODO: Refactor this to use collections
        foreach (config('request-migrations.versions') as $version => $classList) {
            $items = [];

            foreach ($classList as $class) {
                $migration = (new $class());
                if (in_array($routeName, $migration->routes())) {
                    $items[] = $class;
                }
            }

            if (count($items)) {
                $needed[$version] = $items;
            }
        }

        return $needed;
    }
}
