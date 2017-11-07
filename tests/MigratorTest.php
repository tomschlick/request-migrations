<?php

namespace TomSchlick\RequestMigrations\Tests;

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

    /** @test */
    public function it_will_default_to_the_most_recent_version()
    {
        $response = $this->get('users/show');

        $response->assertExactJson([
            'id'     => 123,
            'name'   => [
                'firstname' => 'Dwight',
                'lastname'  => 'Schrute',
            ],
            'title'  => 'Assistant to the Regional Manager',
            'skills' => [
                'bears',
                'beats',
                'battlestar galactica',
            ],
            'request' => [],
        ]);
    }
}
