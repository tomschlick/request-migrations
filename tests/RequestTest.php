<?php

namespace TomSchlick\RequestMigrations\Tests;


class RequestTest extends TestCase
{
    /** @test */
    public function it_can_transform_a_request()
    {
        $response = $this->post(
            'posts',
            [
                'headline' => 'This is going to be an awesome article!',
                'content' => 'Awesome content! I told you!'
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
}