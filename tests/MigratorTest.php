<?php

namespace TomSchlick\RequestMigrations\Tests;

use Illuminate\Support\Facades\Event;
use TomSchlick\RequestMigrations\Events\RequestHasMigrated;
use TomSchlick\RequestMigrations\Events\RequestIsMigrating;
use TomSchlick\RequestMigrations\Events\ResponseHasMigrated;
use TomSchlick\RequestMigrations\Events\ResponseIsMigrating;

class MigratorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Event::fake();
    }

    /** @test */
    public function it_will_only_get_versions_behind_current()
    {
        $this->markTestIncomplete();
    }

    protected function assertMigrationEventsFired()
    {
        Event::assertDispatched([
            RequestIsMigrating::class,
            RequestHasMigrated::class,
            ResponseIsMigrating::class,
            ResponseHasMigrated::class,
        ]);
    }

    protected function assertMigrationEventsDidntFire()
    {
        Event::assertNotDispatched([
            RequestIsMigrating::class,
            RequestHasMigrated::class,
            ResponseIsMigrating::class,
            ResponseHasMigrated::class,
        ]);
    }
}
