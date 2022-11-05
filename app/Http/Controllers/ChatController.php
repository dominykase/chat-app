<?php

namespace App\Http\Controllers;

use App\Services\Screeners\BanScreener;
use App\Services\Screeners\MuteScreener;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;

class ChatController extends Controller
{

    public function messages(Request $request, $roomId)
    {
        $banScreen = new BanScreener();
        if ($banScreen->screen($roomId, Auth::id()))
        {
            return "User is banned from this room";
        }

        return ChatMessage::where('chat_room_id', $roomId)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function newMessage(Request $request, $roomId): string|ChatMessage
    {
        $muteScreen = new MuteScreener();
        if ($muteScreen->screen($roomId, Auth::id()))
        {
            return "User is muted in this room";
        }

        $banScreen = new BanScreener();
        if ($banScreen->screen($roomId, Auth::id()))
        {
            return "User is banned from this room";
        }

        $newMessage = new ChatMessage();
        $newMessage->user_id = Auth::id();
        $newMessage->chat_room_id = $roomId;
        $newMessage->message = $request->message;
        $newMessage->save();

        broadcast(new NewChatMessage($newMessage))->toOthers();

        return $newMessage;
    }
}
