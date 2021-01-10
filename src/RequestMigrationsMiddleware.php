<?php

namespace TomSchlick\RequestMigrations;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestMigrationsMiddleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->requestVersion($request) && ! in_array($this->requestVersion($request), $this->versions())) {
            throw new HttpException(400, 'The request version is invalid');
        }

        if ($this->responseVersion($request) && ! in_array($this->responseVersion($request), $this->versions())) {
            throw new HttpException(400, 'The response version is invalid');
        }

        $migrator = Container::getInstance()->make(Migrator::class)->setRequest($request);

        $migrator->processResponseMigrations(
            $next($migrator->processRequestMigrations())
        );

        $migrator->setResponseHeaders();

        return $migrator->getResponse();
    }

    /**
     * Get all the available versions.
     *
     * @return array
     */
    private function versions(): array
    {
        return array_keys(Config::get('request-migrations.versions'));
    }

    /**
     * Get the request version from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    private function requestVersion(Request $request): string
    {
        return $request->header(Config::get('request-migrations.headers.request-version'), '');
    }

    /**
     * Get the response version from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    private function responseVersion(Request $request): string
    {
        return $request->header(Config::get('request-migrations.headers.response-version'), '');
    }
}
