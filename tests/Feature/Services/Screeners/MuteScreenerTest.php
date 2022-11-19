<?php

namespace Services\Screeners;

use App\Models\RoomUserRelationship;
use App\Services\Screeners\MuteScreener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MuteScreenerTest extends TestCase
{
    use RefreshDatabase;

    public function testMuteScreenGrantsAccessCorrectly()
    {
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);

        $screen = new MuteScreener();
        $this->assertFalse($screen->screen(1, 1));
    }

    public function testMuteScreenDoesNotGrantAccessCorrectly()
    {
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 1, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);

        $screen = new MuteScreener();
        $this->assertTrue($screen->screen(1, 1));
    }
}
