<?php

namespace App\Services\Messages\Responses;

use App\Models\ChatMessage;

class MessageVirtual
{
    private int $id;
    private int $chatRoomId;
    private int $userId;
    private string $userName;
    private string $message;
    private int $canEdit;

    public function __construct(ChatMessage $msg)
    {
        $this->id = $msg->id;
        $this->chatRoomId = $msg->chat_room_id;
        $this->userId = $msg->user_id;
        $this->message = $msg->message;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chatRoomId' => $this->chatRoomId,
            'userId' => $this->userId,
            'userName' => $this->userName,
            'message' => $this->message,
            'canEdit' => $this->canEdit
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getChatRoomId(): int
    {
        return $this->chatRoomId;
    }

    public function setChatRoomId(int $chatRoomId): void
    {
        $this->chatRoomId = $chatRoomId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getCanEdit(): int
    {
        return $this->canEdit;
    }

    public function setCanEdit(int $canEdit): void
    {
        $this->canEdit = $canEdit;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }


}
