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
    abstract public function migrateRequest(Request $request) : Request;

    /**
     * Migrate the response to display to the client.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return \Illuminate\Http\Response
     */
    abstract public function migrateResponse(Response $response) : Response;

    /**
     * Define which named routes should this migration modify.
     *
     * @return array
     */
    abstract public function routes() : array;
}
