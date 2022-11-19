<?php

declare(strict_types=1);

namespace App\Services\ChatRooms;

use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Repositories\ChatRoom\ChatRoomRepository;
use Illuminate\Support\Facades\Auth;

class ChatRoomService
{
    public function __construct(
        private ChatRoomRepository $repository,
        private array $screens
    )
    {
    }

    public function getRooms($userId): array
    {
        $roomIds = $this->repository->getRoomIdsByUserId($userId);
        $rooms = ChatRoom::all()->toArray();

        $filteredRooms = array_values(array_filter($rooms, function ($room) use ($roomIds) {
            return in_array($room['id'], $roomIds);
        }));

        // add virtual values to returned resources
        $returnedRooms = [];
        foreach ($filteredRooms as $room) {
            $relationship = $this->repository->getSingleRelationship($room['id'], $userId);
            $room['is_banned'] = $relationship->is_banned;
            $room['is_muted'] = $relationship->is_muted;
            $room['is_mod'] = $relationship->is_mod;
            $room['unread_messages'] = $relationship->unread_count;
            $returnedRooms[] = $room;
        }

        return $returnedRooms;
    }

    public function createNewChatRoom(
        string $roomName,
        int $isPrivate,
        int $userId
    ): ChatRoom
    {
        $room = $this->repository->createRoom($roomName, $isPrivate);

        // create user-room relationships
        if ($isPrivate)
        {
            $this->repository->createRelationship($room->id, $userId, 1);
        } else
        {
            $users = $this->repository->getAllUsers();
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

    public function getUsersByRoomId(int $roomId): array
    {
        $userIds = $this->repository->getRoomUserIds($roomId)->toArray();
        $allUsers = $this->repository->getAllUsers()->toArray();

        $returnArray = [];
        $returnArray['users'] = array_values(array_filter($allUsers, function($user) use ($userIds) {
            return in_array($user['id'], $userIds);
        }));
        $returnArray['relationships'] = $this->repository->getRelationshipsByRoomId($roomId)->toArray();

        return $returnArray;
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

        $room = $this->repository->getChatRoomById($roomId);

        if ($room->is_private)
        {
            if ($this->repository->getSingleRelationship($roomId, $userId) === null)
            {
                $this->repository->createRelationship($roomId, $userId, 0);
            }

            return "Added or already exists.";
        }
        else
        {
            return "Room is public, cannot add users";
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

        $relationship = $this->repository->getSingleRelationship($roomId, $userId);
        $this->repository->updateRelationship($relationship, $mute, $ban);

        return $relationship;
    }
}
