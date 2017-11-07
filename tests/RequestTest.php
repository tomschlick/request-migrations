<?php

namespace TomSchlick\RequestMigrations\Tests;


class RequestTest extends TestCase
{
    /** @test */
    public function it_can_transform_a_request()
    {
        $response = $this->post(
            'request-test',
            [
                'key' => 'value'
            ],
            [
                'x-api-request-version'  => '2017-02-02',
                'x-api-response-version' => '2017-02-02',
            ]
        );

        $response->assertJson([
            'request' => ['foo' => 'value']
        ]);
    }
}