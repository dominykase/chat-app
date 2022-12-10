<?php

declare(strict_types=1);

namespace App\Repositories\Messages;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MessageRepository implements RepositoryInterface
{
    public function getRoomSubjects(int $roomId): Collection
    {
        return ChatMessage::where('chat_room_id', $roomId)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function get(int $id): ChatMessage
    {
        return ChatMessage::where('id', $id)->get()->first();
    }

    public function create(
        ?int $roomId = null,
        ?int $userId = null,
        ?string $message = null,
        ?string $name = null,
        ?int $isPrivate = null
    ): ChatMessage|ChatRoom
    {
        $newMessage = new ChatMessage();
        $newMessage->user_id = $userId;
        $newMessage->chat_room_id = $roomId;
        $newMessage->message = $message;
        $newMessage->save();

        return $newMessage;
    }

    public function update(int $id, string $message): ChatMessage
    {
        ChatMessage::where('id', $id)->update([
            'message' => $message
        ]);

        return ChatMessage::where('id', $id)->first();
    }

    public function getRoomRelationships(int $roomId): Collection
    {
        return RoomUserRelationship::where('room_id', $roomId)
            ->get();
    }

    public function getRelationship(int $roomId, int $userId): RoomUserRelationship
    {
        return RoomUserRelationship::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->get()->first();
    }

    public function createRelationship(int $roomId, int $userId, int $isMod): RoomUserRelationship
    {
        // TODO: Implement createRelationship() method.
    }

    public function updateRelationship(
        RoomUserRelationship &$relationship,
        int $unreadCount,
        int $mute = null,
        int $ban = null
    ): void
    {
        $relationship->update([
            'unread_count' => $unreadCount
        ]);
    }
}
