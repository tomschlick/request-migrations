<?php

namespace TomSchlick\RequestMigrations\Tests;

use Symfony\Component\HttpKernel\Exception\HttpException;
use TomSchlick\RequestMigrations\Facades\RequestMigrations;

class RequestTest extends TestCase
{
    /** @test */
    public function it_can_transform_a_request()
    {
        $response = $this->post(
            'posts',
            [
                'headline' => 'This is going to be an awesome article!',
                'content' => 'Awesome content! I told you!',
            ],
            [
                'x-api-request-version'  => '2017-01-01',
                'x-api-response-version' => '2017-01-01',
            ]
        );

        $response->assertJson([
            'request' => [
                'title' => 'This is going to be an awesome article!',
                'body' => 'Awesome content! I told you!',
            ],
        ]);
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
    public function request_versions_can_be_manually_set()
    {
        RequestMigrations::setRequestVersion('2017-01-01');

        $response = $this->get('/users/show', [
            'x-api-request-version'  => '2017-04-04',
            'x-api-response-version' => '2017-01-01',
        ]);

        $response->assertJson([
            'id' => 123,
                'firstname' => 'Dwight',
                'lastname'  => 'Schrute',
        ]);

        $response->assertHeader('x-api-request-version', '2017-01-01');
        $response->assertHeader('x-api-response-version', '2017-01-01');
    }
}
