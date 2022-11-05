<?php

declare(strict_types=1);

namespace App\Services\ChatRooms;

use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Repositories\ChatRoom\ChatRoomRepository;

class ChatRoomService
{
    public function __construct(private ChatRoomRepository $repository)
    {
    }

    public function getRooms($userId): array
    {
        return $this->repository->getRoomsByUserId($userId);
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
            $this->repository->createRelationship($room->id, $userId);
        } else
        {
            $users = $this->repository->getAllUsers();
            foreach($users as $user)
            {
                $this->repository->createRelationship($room->id, $user->id);
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
        $room = $this->repository->getChatRoomById();

        if ($room->is_private)
        {
            if ($this->repository->getSingleRelationship() === null)
            {
                $this->repository->createRelationship($roomId, $userId);
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
        $relationship = $this->repository->getSingleRelationship($roomId, $userId);
        $this->repository->updateRelationship($relationship, $mute, $ban);

        return $relationship;
    }
}
