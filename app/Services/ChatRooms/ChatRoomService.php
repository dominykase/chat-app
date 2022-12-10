<?php

declare(strict_types=1);

namespace App\Services\ChatRooms;

use App\Events\ChatRoomsUpdated;
use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Models\User;
use App\Repositories\ChatRoom\ChatRoomRepository;
use App\Repositories\RepositoryInterface;
use App\Services\ChatRooms\Responses\ChatRoomVirtual;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ChatRoomService
{
    public function __construct(
        private RepositoryInterface $repository,
        private array $screens
    )
    {
    }

    public function getRooms($userId): Collection
    {
        $roomIds = RoomUserRelationship::where('user_id', $userId)
            ->pluck('room_id')->toArray();
        $rooms = ChatRoom::all()->filter(function ($room) use ($roomIds) {
            return in_array($room->id, $roomIds);
        });

        $roomsResponse = collect([]);
        foreach ($rooms as $room) {
            $roomVirtual = new ChatRoomVirtual($room);
            $relationship = $this->repository->getRelationship($room['id'], $userId);
            $roomVirtual->setIsBanned($relationship->is_banned);
            $roomVirtual->setIsMuted($relationship->is_muted);
            $roomVirtual->setIsModerator($relationship->is_mod);
            $roomVirtual->setUnreadMessageCount($relationship->unread_count);
            $roomsResponse->push($roomVirtual);
        }

        return $roomsResponse;
    }

    public function createNewChatRoom(
        string $roomName,
        int $isPrivate,
        int $userId
    ): ChatRoom
    {
        $room = $this->repository->create(name: $roomName, isPrivate: $isPrivate);

        // create user-room relationships
        if ($isPrivate)
        {
            $this->repository->createRelationship($room->id, $userId, 1);
        } else
        {
            $users = User::all();
            foreach($users as $user)
            {
                $this->repository->createRelationship(
                    $room->id,
                    $user->id,
                    $user->id === $userId ? 1 : 0
                );
            }
        }

        return $room;
    }

    public function getUsersByRoomId(int $roomId): Collection
    {
        $userIds = $this->repository->getRoomSubjects($roomId)->toArray();
        $users = User::all()->filter(function ($user) use ($userIds) {
            return in_array($user['id'], $userIds);
        });

        return collect([
            'users' => $users,
            'relationships' => $this->repository->getRoomRelationships($roomId)
        ]);
    }

    public function addUserToChatRoom(int $roomId, int $userId): string
    {
        foreach($this->screens as $screen)
        {
            if ($screen->screen($roomId, Auth::id()))
            {
                return $screen->message();
            }
        }

        $room = $this->repository->get($roomId);

        if ($room->is_private)
        {
            if ($this->repository->getRelationship($roomId, $userId) === null)
            {
                $this->repository->createRelationship($roomId, $userId, 0);
            }
            ChatRoomsUpdated::dispatch();
            return "Added or already exists.";
        }
        else
        {
            ChatRoomsUpdated::dispatch();
            return "Room is public, cannot add users.";
        }
    }

    public function updateRoomUserStatus(
        int $roomId,
        int $userId,
        int $mute,
        int $ban
    ): RoomUserRelationship
    {
        foreach($this->screens as $screen)
        {
            if ($screen->screen($roomId, Auth::id()))
            {
                return $screen->message();
            }
        }

        $relationship = $this->repository->getRelationship($roomId, $userId);
        $this->repository->updateRelationship(relationship: $relationship, unreadCount: $relationship->unread_count, mute: $mute, ban: $ban);
        ChatRoomsUpdated::dispatch();

        return $relationship;
    }
}
