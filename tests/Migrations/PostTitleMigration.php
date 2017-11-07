<?php

namespace TomSchlick\RequestMigrations\Tests\Migrations;

use Illuminate\Http\Request;
use TomSchlick\RequestMigrations\RequestMigration;

class PostTitleMigration extends RequestMigration
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
        $request['title'] = $request['headline'];
        unset($request['headline']);

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
            'posts',
        ];
    }
}
