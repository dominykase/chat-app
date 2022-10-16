<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function rooms(Request $request)
    {
        return ChatRoom::all();
    }

    public function createChatRoom(Request $request): ChatRoom
    {
        $chatRoom = new ChatRoom;
        $chatRoom->name = $request->roomName;
        $chatRoom->save();

        return $chatRoom;
    }
}
