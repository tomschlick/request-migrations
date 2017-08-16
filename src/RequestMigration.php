<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class RequestMigration
{
    /**
     * Migrate the request for the application to "read".
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Request
     */
    public abstract function migrateRequest(Request $request) : Request;

    /**
     * Migrate the response to display to the client.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return \Illuminate\Http\Response
     */
    public abstract function migrateResponse(Response $response) : Response;

    /**
     * Define which named routes should this migration modify.
     *
     * @return array
     */
    public abstract function routes() : array;
}
