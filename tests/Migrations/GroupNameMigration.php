<?php

namespace TomSchlick\RequestMigrations\Tests\Migrations;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TomSchlick\RequestMigrations\RequestMigration;

class GroupNameMigration extends RequestMigration
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
        return $request;
    }

    /**
     * Migrate the response to display to the client.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return \Illuminate\Http\Response
     */
    public function migrateResponse(Response $response) : Response
    {
        $content = json_decode($response->getContent(), true);

        $content['firstname'] = array_get($content, 'name.firstname');
        $content['lastname'] = array_get($content, 'name.lastname');
        unset($content['name']);

        return $response->setContent(json_encode($content));
    }

    /**
     * Define which named paths should this migration modify.
     *
     * @return array
     */
    public function paths() : array
    {
        return [
            'users/show',
        ];
    }
}
