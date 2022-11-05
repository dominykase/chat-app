<?php

namespace App\Services\Screeners;

use App\Models\RoomUserRelationship;

class BanScreener implements ScreenerInterface
{
    public function screen(int $roomId, int $userId): bool
    {
        return (bool) RoomUserRelationship::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->first()
            ->is_banned;
    }

    public function message(): string
    {
        return "User is banned from this room.";
    }
}
