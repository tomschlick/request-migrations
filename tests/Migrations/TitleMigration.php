<?php

namespace TomSchlick\RequestMigrations\Tests\Migrations;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TomSchlick\RequestMigrations\RequestMigration;

class TitleMigration extends RequestMigration
{
    /**
     * Migrate the request for the application to "read".
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request
     */
    public function migrateRequest(Request $request): Request
    {
        return $request;
    }

    /**
     * Migrate the response to display to the client.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function migrateResponse(Response $response): Response
    {
        $content = json_decode($response->getContent(), true);

        $content['position'] = $content['title'];
        unset($content['title']);

        return $response->setContent(json_encode($content));
    }

    /**
     * Define which named paths should this migration modify.
     *
     * @return array
     */
    public function paths(): array
    {
        return [
            'users/show',
        ];
    }
}
