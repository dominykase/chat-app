<?php

namespace Services\Screeners;

use App\Models\RoomUserRelationship;
use App\Services\Screeners\UserScreener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserScreenerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserScreenGrantsAccessCorrectly()
    {
        $screen = new UserScreener();
        $this->assertFalse($screen->screen(1, 1));
    }

    public function testUserScreenDoesNotGrantAccessCorrectly()
    {
        $screen = new UserScreener();
        $this->assertTrue($screen->screen(2, 1));
    }
}
