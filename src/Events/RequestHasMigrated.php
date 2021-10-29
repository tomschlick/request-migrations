<?php

namespace TomSchlick\RequestMigrations\Events;

use Illuminate\Http\Request;
use TomSchlick\RequestMigrations\RequestMigration;

class RequestHasMigrated
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
     * @var \Illuminate\Http\Request
     */
    public $modifiedRequest;

    /**
     * @param  \TomSchlick\RequestMigrations\RequestMigration  $migration
     * @param  \Illuminate\Http\Request  $originalRequest
     * @param  \Illuminate\Http\Request  $modifiedRequest
     */
    public function __construct(RequestMigration $migration, Request $originalRequest, Request $modifiedRequest)
    {
        $this->migration = $migration;
        $this->originalRequest = $originalRequest;
        $this->modifiedRequest = $modifiedRequest;
    }
}
