<?php

namespace App\Services\Screeners;

class UserScreener implements ScreenerInterface
{
    public function screen(int $authId, int $userId): bool
    {
        return !($authId == $userId);
    }

    public function message(): string
    {
        return 'User cannot edit this message because they are not its creator.';
    }

}
