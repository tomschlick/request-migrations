<?php

namespace TomSchlick\RequestMigrations\Facades;

use Illuminate\Support\Facades\Facade;

class RequestMigrations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'request-migrations';
    }
}
