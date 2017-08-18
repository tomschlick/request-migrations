<?php

namespace TomSchlick\RequestMigrations;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestMigrationsMiddleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
