<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\User;
use App\Services\PrivateChatRoomLinker;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    public function rooms(Request $request)
    {
        $returnedChatRooms = ChatRoom::where('is_private', 0)->get();
        $privateRoomIdsString = User::where('id', Auth::id())->pluck('private_room_ids');
        $privateRoomIdsArray = explode(",", $privateRoomIdsString);

        foreach($privateRoomIdsArray as $id)
        {
            if (strlen($id) > 0)
            {
                $privateRoom = ChatRoom::where('id', $id)->get();
                $returnedChatRooms = $returnedChatRooms->merge($privateRoom);
            }
        }

        return $returnedChatRooms;
    }

    public function createChatRoom(Request $request): ChatRoom
    {
        $chatRoom = new ChatRoom;
        $chatRoom->name = $request->roomName;
        $chatRoom->is_private = $request->private;
        $chatRoom->save();

        if ($request->private)
        {
            $linker = new PrivateChatRoomLinker();
            $linker->link(Auth::id(), $chatRoom->id);
        }


        return $chatRoom;
    }

    public function getUsers(Request $request, $roomId)
    {
        $chatRoom = ChatRoom::where('id', $roomId)->get()->first();
        $allUsers = User::all();
        $linker = new PrivateChatRoomLinker();
        $usersThatBelong = new Collection();

        if ($chatRoom->is_private)
        {
            foreach($allUsers as $user)
            {
                if ($linker->hasUser($user, $chatRoom))
                {
                    $usersThatBelong->push($user);
                }
            }
        }
        else
        {
            $usersThatBelong = $allUsers;
        }


        return $usersThatBelong;
    }
}
