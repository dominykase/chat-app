<?php

declare(strict_types=1);

namespace App\Repositories\ChatRoom;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Models\User;
use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ChatRoomRepository implements RepositoryInterface
{

    public function create(
        ?int $roomId = null,
        ?int $userId = null,
        ?string $message = null,
        ?string $name = null,
        ?int $isPrivate = null
    ): ChatMessage|ChatRoom
    {
        return ChatRoom::create([
            'name' => $name,
            'is_private' => $isPrivate
        ]);
    }

    public function createRelationship(int $roomId, int $userId, int $isMod): RoomUserRelationship
    {
        return RoomUserRelationship::create([
            'room_id' => $roomId,
            'user_id' => $userId,
            'is_muted' => 0,
            'is_banned' => 0,
            'is_mod' => $isMod,
            'unread_count' => 0
        ]);
    }

    public function getRoomSubjects(int $roomId): \Illuminate\Support\Collection
    {
        return RoomUserRelationship::where('room_id', $roomId)
            ->pluck('user_id');
    }

    public function getRoomRelationships(int $roomId): Collection
    {
        return RoomUserRelationship::where('room_id', $roomId)->get();
    }

    public function get(int $id): ChatRoom
    {
        return ChatRoom::where('id', $id)->get()->first();
    }

    public function getRelationship(int $roomId, int $userId): null|RoomUserRelationship
    {
        return RoomUserRelationship::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->get()->first();
    }

    public function updateRelationship(
        ?RoomUserRelationship &$relationship,
        ?int $unreadCount,
        ?int $mute = null,
        ?int $ban = null
    ): void
    {
        $relationship->update([
            'is_muted' => $mute,
            'is_banned' => $ban,
            'unread_count' => $unreadCount
        ]);
    }

    public function update(int $id, string $message): ChatMessage
    {
        // TODO: Implement update() method.
    }
}
