<?php

namespace TomSchlick\RequestMigrations\Tests;

use Symfony\Component\HttpKernel\Exception\HttpException;
use TomSchlick\RequestMigrations\Facades\RequestMigrations;

class MigratorTest extends TestCase
{
    /** @test */
    public function it_will_send_the_current_version()
    {
        $response = $this->get('/users/show', [
            'x-api-request-version'  => '2017-03-03',
            'x-api-response-version' => '2017-03-03',
        ]);

        $response->assertHeader('x-api-current-version', '2017-04-04');
    }
}
