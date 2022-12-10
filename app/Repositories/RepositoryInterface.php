<?php

namespace App\Repositories;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function create(
        int $roomId = null,
        int $userId = null,
        string $message = null,
        string $name = null,
        int $isPrivate = null
    ): ChatMessage|ChatRoom;
    public function createRelationship(int $roomId, int $userId, int $isMod): RoomUserRelationship;
    public function getRoomSubjects(int $roomId): Collection;
    public function getRoomRelationships(int $roomId): Collection;
    public function get(int $id): ChatRoom|ChatMessage;
    public function getRelationship(int $roomId, int $userId): null|RoomUserRelationship;
    public function updateRelationship(
        RoomUserRelationship &$relationship,
        int $unreadCount,
        int $mute = null,
        int $ban = null
    ): void;
    public function update(int $id, string $message): ChatMessage;

}
