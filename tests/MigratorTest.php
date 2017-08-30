<?php

namespace TomSchlick\RequestMigrations\Tests;

use Illuminate\Http\Request;
use TomSchlick\RequestMigrations\Migrator;

class MigratorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_will_only_get_versions_behind_current()
    {
        $this->markTestIncomplete();
    }
}
