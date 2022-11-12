<?php

namespace App\Services\Screeners;

use App\Models\RoomUserRelationship;

class UserScreener implements ScreenerInterface
{
    public function screen(int $authId, int $userId): bool
    {
        return !(bool) $authId == $userId;
    }

    public function message(): string
    {
        return 'User cannot edit this message because they are not its creator.';
    }

}
