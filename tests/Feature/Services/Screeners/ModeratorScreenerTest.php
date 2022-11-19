<?php

namespace Services\Screeners;

use App\Models\RoomUserRelationship;
use App\Services\Screeners\ModeratorScreener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeratorScreenerTest extends TestCase
{
    use RefreshDatabase;

    public function testModeratorScreenGrantsAccessCorrectly()
    {
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);

        $screen = new ModeratorScreener();
        $this->assertFalse($screen->screen(1, 1));
    }

    public function testModeratorScreenDoesNotGrantAccessCorrectly()
    {
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 0, 'unread_count' => 0]);

        $screen = new ModeratorScreener();
        $this->assertTrue($screen->screen(1, 1));
    }
}
