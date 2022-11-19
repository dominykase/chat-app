<?php

namespace Services\Screeners;

use App\Models\RoomUserRelationship;
use App\Services\Screeners\BanScreener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BanScreenerTest extends TestCase
{
    use RefreshDatabase;

    public function testBanScreenGrantsAccessCorrectly()
    {
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);

        $screen = new BanScreener();
        $this->assertFalse($screen->screen(1, 1));
    }

    public function testBanScreenDoesNotGrantAccessCorrectly()
    {
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 1,
            'is_mod' => 1, 'unread_count' => 0]);

        $screen = new BanScreener();
        $this->assertTrue($screen->screen(1, 1));
    }
}
