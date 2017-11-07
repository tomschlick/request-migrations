<?php

namespace TomSchlick\RequestMigrations\Tests\Migrations;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TomSchlick\RequestMigrations\RequestMigration;

class RequestMigrationExample extends RequestMigration
{
    /**
     * Migrate the request for the application to "read".
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Request
     */
    public function migrateRequest(Request $request) : Request
    {
        $request['foo'] = $request['key'];
        unset($request['key']);

        return $request;
    }

    /**
     * Define which named paths should this migration modify.
     *
     * @return array
    */
    public function paths() : array
    {
        return [
            'request-test',
        ];
    }
}
