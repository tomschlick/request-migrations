<?php

namespace TomSchlick\RequestMigrations;

use Closure;
use Illuminate\Http\Request;
use function array_key_exists;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestMigrationsMiddleware
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;
    protected $versions;
    protected $latestMigration;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next) : Response
    {
        $this->request = $request;

        $this->latestMigration = $this->cleanVersion(config('request-migrations.current_version'));

        if(empty($this->latestMigration)) {
            $this->latestMigration = collect($this->versions())->keys()->last();
        }

        if($request->user() && $request->user()->api_version) {
            $this->setRequestVersion($request->user()->api_version);
            $this->setResponseVersion($request->user()->api_version);
        }

        $requestVersion = $this->requestVersion();
        $responseVersion = $this->responseVersion();

        if (
            ($requestVersion !== $this->latestMigration) &&
            ($requestVersion < $this->latestMigration) &&
            ($requestVersion && ! array_key_exists($requestVersion, $this->versions()))
        ) {
            throw new HttpException(400, 'The request version is invalid');
        }

        if (
            ($responseVersion !== $this->latestMigration) &&
            ($responseVersion < $this->latestMigration) &&
            ($responseVersion && ! array_key_exists($responseVersion, $this->versions()))
        ) {

            throw new HttpException(400, 'The response version is invalid');
        }

        $migrator = Container::getInstance()->make(Migrator::class)->setRequest($request);

        return $migrator->processResponseMigrations(
                $next($migrator->processRequestMigrations())
            )
            ->setResponseHeaders()
            ->getResponse();
    }

    /**
     * Get all the available versions.
     *
     * @return array
     */
    private function versions() : array
    {
        if($this->versions) {
            return $this->versions;
        }

        return $this->versions = app()->make('getRequestMigrationsVersions')->toArray();
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
