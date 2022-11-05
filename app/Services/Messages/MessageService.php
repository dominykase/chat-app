<?php

declare(strict_types=1);

namespace App\Services\Messages;

use App\Models\ChatMessage;
use App\Repositories\Messages\MessageRepository;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    public function __construct(private MessageRepository $repository, private array $screens)
    {
    }

    public function getMessages(int $roomId, int $userId): string|Collection
    {
        if ($this->screens[0]->screen($roomId, $userId))
        {
            return $this->screens[0]->message();
        }

        return $this->repository->getRoomMessages($roomId);
    }

    public function createMessage(
        int $roomId,
        int $userId,
        string $message
    ): string|ChatMessage
    {
        foreach($this->screens as $screen)
        {
            if ($screen->screen($roomId, $userId))
            {
                return $screen->message();
            }
        }

        return $this->repository->createMessage($roomId, $userId, $message);
    }

}
