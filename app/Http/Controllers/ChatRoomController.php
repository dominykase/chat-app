<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Models\User;
use App\Repositories\ChatRoom\ChatRoomRepository;
use App\Services\ChatRooms\ChatRoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    public function __construct(private ChatRoomRepository $chatRepository)
    {
    }

    public function rooms(Request $request): JsonResponse
    {
        $service = new ChatRoomService($this->chatRepository);

        return response()->json(
            $service->getRooms(Auth::id())
        );
    }

    public function createChatRoom(Request $request): JsonResponse
    {
        $service = new ChatRoomService($this->chatRepository);

        return response()->json(
            $service->createNewChatRoom(
                $request->roomName,
                $request->private,
                Auth::id()
            )
        );
    }

    public function getUsers(Request $request, int $roomId): JsonResponse
    {
        $service = new ChatRoomService($this->chatRepository);

        return response()->json(
            $service->getUsersByRoomId($roomId)
        );
    }

    public function addUser(Request $request, int $roomId): JsonResponse
    {
        $service = new ChatRoomService($this->chatRepository);

        return response()->json(
            $service->addUserToChatRoom($roomId, $request->userId)
        );
    }

    public function updateUserStatus(Request $request, int $roomId): JsonResponse
    {
        $service = new ChatRoomService($this->chatRepository);

        return response()->json(
            $service->updateRoomUserStatus(
                $roomId,
                $request->userId,
                $request->mute ? 1 : 0,
                $request->ban ? 1 : 0
            )
        );
    }
}
