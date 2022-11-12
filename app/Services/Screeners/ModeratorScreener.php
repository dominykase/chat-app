<?php

namespace App\Services\Screeners;

use App\Models\RoomUserRelationship;

class ModeratorScreener implements ScreenerInterface
{
    public function screen(int $roomId, int $userId): bool
    {
        return !(bool) RoomUserRelationship::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->first()
            ->is_mod;
    }

    public function message(): string
    {
        return 'User is not a moderator of this room.';
    }

}
