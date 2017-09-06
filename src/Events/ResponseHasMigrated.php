<?php

namespace TomSchlick\RequestMigrations\Events;

use Symfony\Component\HttpFoundation\Response;
use TomSchlick\RequestMigrations\RequestMigration;

class ResponseHasMigrated
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
     * @var \Symfony\Component\HttpFoundation\Response
     */
    public $modifiedResponse;

    /**
     * @param \TomSchlick\RequestMigrations\RequestMigration $migration
     * @param \Symfony\Component\HttpFoundation\Response     $originalResponse
     * @param \Symfony\Component\HttpFoundation\Response     $modifiedResponse
     */
    public function __construct(RequestMigration $migration, Response $originalResponse, Response $modifiedResponse)
    {
        $this->migration = $migration;
        $this->originalResponse = $originalResponse;
        $this->modifiedResponse = $modifiedResponse;
    }
}
