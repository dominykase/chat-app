<?php

declare(strict_types=1);

namespace App\Repositories\Messages;

use App\Models\ChatMessage;
use App\Models\RoomUserRelationship;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MessageRepository
{
    public function getRoomMessages(int $roomId): Collection
    {
        return ChatMessage::where('chat_room_id', $roomId)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function createMessage(
        int $roomId,
        int $userId,
        string $message
    ): ChatMessage
    {
        $newMessage = new ChatMessage();
        $newMessage->user_id = $userId;
        $newMessage->chat_room_id = $roomId;
        $newMessage->message = $message;
        $newMessage->save();

        return $newMessage;
    }

    public function updateMessage(
        int $messageId,
        string $message
    ): ChatMessage
    {
        ChatMessage::where('id', $messageId)->update([
            'message' => $message
        ]);

        return ChatMessage::where('id', $messageId)->first();
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

    public function updateRelationship(RoomUserRelationship &$relationship, int $unreadCount): void
    {
        $relationship->update([
            'unread_count' => $unreadCount
        ]);
    }
}
