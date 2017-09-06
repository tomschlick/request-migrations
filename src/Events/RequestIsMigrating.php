<?php

namespace TomSchlick\RequestMigrations\Events;

use Illuminate\Http\Request;
use TomSchlick\RequestMigrations\RequestMigration;

class RequestIsMigrating
{
    /**
     * @var \TomSchlick\RequestMigrations\RequestMigration
     */
    public $migration;

    /**
     * @var \Illuminate\Http\Request
     */
    public $originalRequest;

    /**
     * @param \TomSchlick\RequestMigrations\RequestMigration $migration
     * @param \Illuminate\Http\Request                       $originalRequest
     */
    public function __construct(RequestMigration $migration, Request $originalRequest)
    {
        $this->migration = $migration;
        $this->originalRequest = $originalRequest;
    }
}
