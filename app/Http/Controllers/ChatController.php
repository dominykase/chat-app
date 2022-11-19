<?php

namespace App\Http\Controllers;

use App\Events\ChatRoomsUpdated;
use App\Models\ChatRoom;
use App\Repositories\Messages\MessageRepository;
use App\Services\Messages\MessageService;
use App\Services\Screeners\BanScreener;
use App\Services\Screeners\MuteScreener;
use App\Services\Screeners\UserScreener;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;

class ChatController extends Controller
{

    public function __construct(private MessageRepository $messageRepository)
    {
    }

    public function messages(Request $request, $roomId): JsonResponse
    {
        $service = new MessageService($this->messageRepository, [
            new BanScreener()
        ]);
        $messages = $service->getMessages($roomId, Auth::id());

        ChatRoomsUpdated::dispatch();

        return response()->json($messages);
    }

    public function newMessage(Request $request, $roomId): string|ChatMessage
    {
        $service = new MessageService($this->messageRepository, [
            new MuteScreener(),
            new BanScreener()
        ]);

        $message = $service->createMessage($roomId, Auth::id(), $request->message);

        if ($message instanceof ChatMessage)
        {
            NewChatMessage::dispatch($message);
            ChatRoomsUpdated::dispatch();
        }

        return response()->json($message);
    }

    public function updateMessage(Request $request, $roomId): string|ChatMessage
    {
        $service = new MessageService($this->messageRepository, [
            new UserScreener()
        ]);

        $message = $service->updateMessage(
            $request->messageId,
            $request->userId,
            Auth::id(),
            $request->message
        );

        if ($message instanceof ChatMessage)
        {
            NewChatMessage::dispatch($message);
        }

        return $message;
    }
}
