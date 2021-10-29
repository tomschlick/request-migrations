<?php

namespace TomSchlick\RequestMigrations\Events;

use Symfony\Component\HttpFoundation\Response;
use TomSchlick\RequestMigrations\RequestMigration;

class ResponseIsMigrating
{
    /**
     * @var \TomSchlick\RequestMigrations\RequestMigration
     */
    public $migration;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    public $originalResponse;

    /**
     * @param  \TomSchlick\RequestMigrations\RequestMigration  $migration
     * @param  \Symfony\Component\HttpFoundation\Response  $originalResponse
     */
    public function __construct(RequestMigration $migration, Response $originalResponse)
    {
        $this->migration = $migration;
        $this->originalResponse = $originalResponse;
    }
}
