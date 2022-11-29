<?php

declare(strict_types=1);

namespace App\Services\Messages;

use App\Models\ChatMessage;
use App\Repositories\Messages\MessageRepository;
use App\Services\Screeners\UserScreener;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    public function __construct(private MessageRepository $repository, private array $screens)
    {
    }

    public function getMessages(int $roomId, int $userId): string|array
    {
        foreach($this->screens as $screen) {
            if ($screen->screen($roomId, $userId)) {
                return $screen->message();
            }
        }

        $relationship = $this->repository->getRelationship($roomId, $userId);
        $this->repository->updateRelationship($relationship, 0);

        $rawMessages = $this->repository->getRoomMessages($roomId)->toArray();
        $messagesWithVirtualData = [];

        foreach ($rawMessages as $message)
        {
            $message['canEdit'] = $userId === $message['user_id'] ? 1 : 0;
            $messagesWithVirtualData[] = $message;
        }

        return $messagesWithVirtualData;
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

        $relationships = $this->repository->getRoomRelationships($roomId);
        foreach($relationships as $relationship) {
            $unreadCount = $relationship->unread_count;
            $this->repository->updateRelationship($relationship, $unreadCount + 1);
        }

        return $this->repository->createMessage($roomId, $userId, $message);
    }

    public function updateMessage(
        int $messageId,
        int $roomId,
        int $userId,
        int $authId,
        string $message
    ): string|ChatMessage
    {
        foreach($this->screens as $screen) {
            if ($screen instanceof UserScreener && $screen->screen($authId, $userId)) {
                return $screen->message();
            } else if ($screen->screen($roomId, $authId)) {
                return $screen->message();
            }
        }

        return $this->repository->updateMessage($messageId, $message);
    }

}
