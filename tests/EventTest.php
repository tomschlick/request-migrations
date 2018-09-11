<?php

namespace TomSchlick\RequestMigrations\Tests;

use Illuminate\Support\Facades\Event;
use TomSchlick\RequestMigrations\Events\RequestHasMigrated;
use TomSchlick\RequestMigrations\Events\RequestIsMigrating;
use TomSchlick\RequestMigrations\Tests\Migrations\PostBodyMigration;
use TomSchlick\RequestMigrations\Tests\Migrations\GroupNameMigration;
use TomSchlick\RequestMigrations\Tests\Migrations\PostTitleMigration;

class EventTest extends TestCase
{
    /** @test */
    public function event_are_emitted_on_migration()
    {
        // Given
        Event::fake();

        $response = $this->post(
            'posts',
            [
                'headline' => 'This is going to be an awesome article!',
                'content'  => 'Awesome content! I told you!',
            ],
            [
                'x-api-request-version'  => '2017-01-01',
                'x-api-response-version' => '2017-01-01',
            ]
        );

        $expectedEvents = [
            PostBodyMigration::class,
            GroupNameMigration::class,
            PostTitleMigration::class,
        ];

        Event::assertDispatched(RequestIsMigrating::class, function (RequestIsMigrating $event) use (&$expectedEvents) {
            $migration = get_class($event->migration);

            // Remove the emitted event from expected array
            if (($key = array_search($migration, $expectedEvents)) !== false) {
                unset($expectedEvents[$key]);
            }

            return true;
        });

        $this->assertCount(0, $expectedEvents);

        $expectedEvents = [
            PostBodyMigration::class,
            GroupNameMigration::class,
            PostTitleMigration::class,
        ];

        Event::assertDispatched(RequestHasMigrated::class, function (RequestHasMigrated $event) use (&$expectedEvents) {
            $migration = get_class($event->migration);

            // Remove the emitted event from expected array
            if (($key = array_search($migration, $expectedEvents)) !== false) {
                unset($expectedEvents[$key]);
            }

            return true;
        });

        $this->assertCount(0, $expectedEvents);

        $response->assertJson([
            'request' => [
                'title' => 'This is going to be an awesome article!',
                'body'  => 'Awesome content! I told you!',
            ],
        ]);
    }
}
