<?php

namespace TomSchlick\RequestMigrations\Tests;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestAndResponseTest extends TestCase
{
    /** @test */
    public function it_will_get_an_unmodified_user_object()
    {
        $response = $this->get('/users/show', [
            'x-api-request-version'  => '2017-03-03',
            'x-api-response-version' => '2017-03-03',
        ]);

        $response->assertJson([
            'id'   => 123,
            'name' => [
                'firstname' => 'Dwight',
                'lastname'  => 'Schrute',
            ],
        ]);

        $response->assertHeader('x-api-request-version', '2017-03-03');
        $response->assertHeader('x-api-response-version', '2017-03-03');
    }

    /** @test */
    public function it_will_get_a_modified_user_object()
    {
        $response = $this->get('/users/show', [
            'x-api-request-version'  => '2017-01-01',
            'x-api-response-version' => '2017-01-01',
        ]);

        $response->assertJson([
            'id'        => 123,
            'firstname' => 'Dwight',
            'lastname'  => 'Schrute',
        ]);

        $response->assertHeader('x-api-request-version', '2017-01-01');
        $response->assertHeader('x-api-response-version', '2017-01-01');
    }

    /** @test */
    public function it_will_throw_an_exception_if_the_request_version_is_invalid()
    {
        $this->expectException(HttpException::class);

        $this->get('/users/show', [
            'x-api-request-version'  => '2016-03-03',
        ])->json();
    }

    /** @test */
    public function it_will_throw_an_exception_if_the_response_version_is_invalid()
    {
        $this->expectException(HttpException::class);

        $this->get('/users/show', [
            'x-api-response-version'  => '2016-03-03',
        ])->json();
    }
}
