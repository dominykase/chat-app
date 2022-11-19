<?php

declare(strict_types=1);

namespace App\Repositories\ChatRoom;

use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatRoomRepository
{
    public function getRoomIdsByUserId($userId): array
    {
        return RoomUserRelationship::where('user_id', $userId)
            ->pluck('room_id')->toArray();
    }

    public function createRoom(string $name, int $isPrivate): ChatRoom
    {
        $chatRoom = new ChatRoom();
        $chatRoom->name = $name;
        $chatRoom->is_private = $isPrivate;
        $chatRoom->save();

        return $chatRoom;
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

    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function getRoomUserIds(int $roomId): \Illuminate\Support\Collection
    {
        return RoomUserRelationship::where('room_id', $roomId)
            ->pluck('user_id');
    }

    public function getRelationshipsByRoomId(int $roomId): Collection
    {
        return RoomUserRelationship::where('room_id', $roomId)->get();
    }

    public function getChatRoomById(int $roomId): ChatRoom
    {
        return ChatRoom::where('id', $roomId)->get()->first();
    }

    public function getSingleRelationship(int $roomId, int $userId): null|RoomUserRelationship
    {
        return RoomUserRelationship::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->get()->first();
    }

    public function updateRelationship(
        RoomUserRelationship &$relationship,
        int $mute,
        int $ban,
        int $unreadCount
    ): void
    {
        $relationship->update([
            'is_muted' => $mute,
            'is_banned' => $ban,
            'unread_count' => $unreadCount
        ]);
    }
}
