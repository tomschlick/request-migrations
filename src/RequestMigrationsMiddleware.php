<?php

namespace TomSchlick\RequestMigrations;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestMigrationsMiddleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next) : Response
    {
        $migrator = new Migrator($request, config('request-migrations'));

        $migrator->processResponseMigrations(
            $next(
                $migrator->processRequestMigrations()
            )
        );

        $migrator->setResponseHeaders();

        return $migrator->getResponse();
    }
}
