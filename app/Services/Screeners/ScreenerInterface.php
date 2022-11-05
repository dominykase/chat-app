<?php

declare(strict_types=1);

namespace App\Services\Screeners;

interface ScreenerInterface
{
    public function screen(int $roomId, int $userId): bool;
    public function message(): string;
}
