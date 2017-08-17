<?php

namespace TomSchlick\RequestMigrations;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param \Symfony\Component\HttpFoundation\Response$response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    abstract public function migrateResponse(Response $response) : Response;

    /**
     * Define which named paths should this migration modify.
     *
     * @return array
     */
    abstract public function paths() : array;
}
