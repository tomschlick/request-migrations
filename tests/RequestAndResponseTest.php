<?php

namespace TomSchlick\RequestMigrations\Tests;

use Symfony\Component\HttpKernel\Exception\HttpException;
use TomSchlick\RequestMigrations\Facades\RequestMigrations;

class RequestAndResponseTest extends TestCase
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

    /** @test */
    public function request_and_response_versions_can_be_manually_set()
    {
        RequestMigrations::setVersion('2017-04-04');
        $response = $this->get('/users/show', [
            'x-api-request-version'  => '2017-01-01',
            'x-api-response-version' => '2017-01-01',
        ]);
        $response->assertJson([
            'id'        => 123,
            'name' => [
                'firstname' => 'Dwight',
                'lastname'  => 'Schrute',
            ],
        ]);
        $response->assertHeader('x-api-request-version', '2017-04-04');
        $response->assertHeader('x-api-response-version', '2017-04-04');
    }
}
