<?php

declare(strict_types=1);

namespace App\Services\Messages;

use App\Events\UserIsTyping;
use App\Models\ChatMessage;
use App\Models\User;
use App\Repositories\Messages\MessageRepository;
use App\Repositories\RepositoryInterface;
use App\Services\Messages\Responses\MessageVirtual;
use App\Services\Screeners\UserScreener;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    public function __construct(private RepositoryInterface $repository, private array $screens)
    {
    }

    public function getMessages(int $roomId, int $userId): string|\Illuminate\Support\Collection
    {
        foreach($this->screens as $screen) {
            if ($screen->screen($roomId, $userId)) {
                return $screen->message();
            }
        }

        $relationship = $this->repository->getRelationship($roomId, $userId);
        $this->repository->updateRelationship($relationship, 0);

        $rawMessages = $this->repository->getRoomSubjects($roomId);

        $response = collect([]);
        foreach ($rawMessages as $message)
        {
            $messageVirtual = new MessageVirtual($message);
            $messageVirtual->setCanEdit($userId === $message['user_id'] ? 1 : 0);
            $messageVirtual->setUserName(
                User::where('id', $messageVirtual->getUserId())->get()->first()->name
            );
            $response->push($messageVirtual);
        }

        return $response;
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

        return $this->repository->create($roomId, $userId, $message);
    }

    public function updateMessage(
        int $messageId,
        int $authId,
        string $message
    ): string|ChatMessage
    {
        $chatMessage = $this->repository->get($messageId);

        foreach($this->screens as $screen) {
            if ($screen instanceof UserScreener && $screen->screen($authId, $chatMessage->user_id)) {
                return $screen->message();
            } else if (!($screen instanceof UserScreener) && $screen->screen($chatMessage->chat_room_id, $authId)) {
                return $screen->message();
            }
        }

        return $this->repository->update($messageId, $message);
    }

    public function dispatchUserIsTyping(int $roomId, int $userId): void
    {
        $user = User::where('id', $userId)->get()->first();
        broadcast(new UserIsTyping($user->name, $roomId))->toOthers();
    }

}
