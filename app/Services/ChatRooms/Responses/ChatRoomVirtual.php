<?php

namespace App\Services\ChatRooms\Responses;

use App\Models\ChatRoom;

class ChatRoomVirtual
{
    private int $id;
    private string $name;
    private int $isPrivate;
    private int $isBanned;
    private int $isMuted;
    private int $isModerator;
    private int $unreadMessageCount;

    public function __construct(ChatRoom $room)
    {
        $this->id = $room->id;
        $this->name = $room->name;
        $this->isPrivate = $room->is_private;
    }

    public function toArray(): array
    {
        $arr = [
            'id' => $this->id,
            'name' => $this->name,
            'isPrivate' => $this->isPrivate,
            'isBanned' => $this->isBanned,
            'isMuted' => $this->isMuted,
            'isModerator' => $this->isModerator,
            'unreadMessageCount' => $this->unreadMessageCount
        ];

        return $arr;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setIsPrivate(int $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

    public function setIsBanned(int $isBanned): void
    {
        $this->isBanned = $isBanned;
    }

    public function setIsMuted(int $isMuted): void
    {
        $this->isMuted = $isMuted;
    }

    public function setIsModerator(int $isModerator): void
    {
        $this->isModerator = $isModerator;
    }

    public function setUnreadMessageCount(int $unreadMessageCount): void
    {
        $this->unreadMessageCount = $unreadMessageCount;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsPrivate(): int
    {
        return $this->isPrivate;
    }

    public function getIsBanned(): int
    {
        return $this->isBanned;
    }

    public function getIsMuted(): int
    {
        return $this->isMuted;
    }

    public function getIsModerator(): int
    {
        return $this->isModerator;
    }

    public function getUnreadMessageCount(): int
    {
        return $this->unreadMessageCount;
    }

    public function getId(): int
    {
        return $this->id;
    }

}
