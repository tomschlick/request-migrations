<?php

namespace TomSchlick\RequestMigrations;

use Closure;
use Illuminate\Http\Request;
use function array_key_exists;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestMigrationsMiddleware
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next) : Response
    {
        $this->request = $request;

        if($request->user() && $request->user()->api_version) {
            $this->setRequestVersion($request->user()->api_version);
            $this->setResponseVersion($request->user()->api_version);
        }

        $requestVersion = $this->requestVersion();
        $responseVersion = $this->responseVersion();

        if ($requestVersion && ! array_key_exists($requestVersion, $this->versions())) {
            throw new HttpException(400, 'The request version is invalid');
        }

        if ($responseVersion && ! array_key_exists($responseVersion, $this->versions())) {
            throw new HttpException(400, 'The response version is invalid');
        }

        $migrator = new Migrator($request, config('request-migrations'));

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
    private function versions() : array
    {
        return app()->make('getRequestMigrationsVersions')->toArray();
    }

    /**
     * Get the request version from the request.
     *
     * @return string
     */
    private function requestVersion() : string
    {
        return $this->request->header(config('request-migrations.headers.request-version'), '');
    }

    /**
     * Get the response version from the request.
     *
     * @return string
     */
    private function responseVersion() : string
    {
        return $this->request->header(config('request-migrations.headers.response-version'), '');
    }

    /**
     * @param $version
     */
    private function setRequestVersion($version)
    {
        $this->request->headers->set(config('request-migrations.headers.request-version'), $this->cleanVersion($version));
    }

    /**
     * @param $version
     */
    private function setResponseVersion($version)
    {
        $this->request->headers->set(config('request-migrations.headers.response-version'), $this->cleanVersion($version));
    }

    /**
     * @param $version
     * @return mixed
     */
    private function cleanVersion($version)
    {
        return str_replace('-', '_', $version);
    }
}
