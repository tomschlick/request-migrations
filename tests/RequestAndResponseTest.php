<?php

namespace TomSchlick\RequestMigrations\Tests;

use Symfony\Component\HttpKernel\Exception\HttpException;
use TomSchlick\RequestMigrations\Facades\RequestMigrations;

class RequestAndResponseTest extends TestCase
{
    /** @test */
    public function it_will_migrate_through_response_multiple_versions()
    {
        $response = $this->get('/users/show', [
            'x-api-request-version'  => '2017-02-02',
            'x-api-response-version' => '2017-02-02',
        ]);

        $response->assertJson([
            'id'   => 123,
            'firstname' => 'Dwight',
            'lastname'  => 'Schrute',
            'position' => 'Assistant to the Regional Manager',
        ]);

        $this->assertArrayNotHasKey('title', $response->json());
        $this->assertArrayNotHasKey('name', $response->json());

        $response->assertHeader('x-api-request-version', '2017-02-02');
        $response->assertHeader('x-api-response-version', '2017-02-02');
    }

    /** @test */
    public function it_does_not_run_a_response_migration_for_the_requested_version()
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
            'position' => 'Assistant to the Regional Manager',
        ]);

        $this->assertArrayNotHasKey('title', $response->json());

        $response->assertHeader('x-api-request-version', '2017-03-03');
        $response->assertHeader('x-api-response-version', '2017-03-03');
    }

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
    public function it_will_get_a_modified_response_object()
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

    /** @test */
    public function response_versions_can_be_manually_set()
    {
        RequestMigrations::setResponseVersion('2017-01-01');

        $response = $this->get('/users/show', [
            'x-api-request-version'  => '2017-01-01',
            'x-api-response-version' => '2017-04-04',
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
    public function request_versions_can_be_manually_set()
    {
        RequestMigrations::setRequestVersion('2017-01-01');

        /* @TODO we should add a POST endpoint and migration to test request transformations */
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
