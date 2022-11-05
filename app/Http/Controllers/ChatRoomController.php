<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Models\User;
use App\Services\PrivateChatRoomLinker;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    public function rooms(Request $request): array
    {
        $roomIds = RoomUserRelationship::where('user_id', Auth::id())
            ->pluck('room_id')->toArray();
        $rooms = ChatRoom::all()->toArray();

        $filteredRooms = array_values(array_filter($rooms, function($room) use ($roomIds) {
            return in_array($room['id'], $roomIds);
        }));

        // add virtual values to returned resources
        $returnedRooms = [];
        foreach($filteredRooms as $room)
        {
            $relationship = RoomUserRelationship::where('user_id', Auth::id())
                ->where('room_id', $room['id'])->first();
            $room['is_banned'] = $relationship->is_banned;
            $room['is_muted'] = $relationship->is_muted;
            $returnedRooms[] = $room;
        }

        return $returnedRooms;
    }

    public function createChatRoom(Request $request): ChatRoom
    {
        $chatRoom = new ChatRoom();
        $chatRoom->name = $request->roomName;
        $chatRoom->is_private = $request->private;
        $chatRoom->save();

        if ($request->private)
        {
            RoomUserRelationship::create([
                'room_id' => $chatRoom->id,
                'user_id' => Auth::user()->id,
                'is_muted' => 0,
                'is_banned' => 0
            ]);
        } else
        {
            $users = User::all();
            foreach($users as $user)
            {
                RoomUserRelationship::create([
                    'room_id' => $chatRoom->id,
                    'user_id' => $user->id,
                    'is_muted' => 0,
                    'is_banned' => 0
                ]);
            }
        }

        return $chatRoom;
    }

    public function getUsers(Request $request, $roomId): array
    {
        $relationshipQuery = RoomUserRelationship::where('room_id', $roomId);
        $userIds = $relationshipQuery->pluck('user_id')->toArray();
        $allUsers = User::all()->toArray();

        $returnArray = [];
        $returnArray['users'] = array_values(array_filter($allUsers, function($user) use ($userIds) {
            return in_array($user['id'], $userIds);
        }));
        $returnArray['relationships'] = $relationshipQuery->get()->toArray();

        return $returnArray;
    }

    public function addUser(Request $request, $roomId): string
    {
        $userId = $request->userId;
        $chatRoom = ChatRoom::where('id', $roomId)->get()->first();

        if ($chatRoom->is_private)
        {
            RoomUserRelationship::create([
                'room_id' => $chatRoom->id,
                'user_id' => $userId,
                'is_muted' => 0,
                'is_banned' => 0
            ]);
            return "Added or already exists.";
        }
        else
        {
            return "Room is public, cannot add users";
        }
    }

    public function updateUserStatus(Request $request, $roomId): RoomUserRelationship
    {
        $relationship = RoomUserRelationship::where('user_id', $request->userId)
            ->where('room_id', $roomId)->first();
        $relationship->update([
            'is_muted' => $request->mute ? 1 : 0,
            'is_banned' => $request->ban ? 1 : 0
        ]);

        return $relationship;
    }
}
