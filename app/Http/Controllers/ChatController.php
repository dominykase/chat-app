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

        // convert objects to arrays so they can be JSON serialized
        $response = [];
        foreach ($messages as $message) {
            $response[] = $message->toArray();
        }

        return response()->json($response);
    }

    public function newMessage(Request $request, int $roomId): string|ChatMessage
    {
        $service = new MessageService($this->messageRepository, [
            new MuteScreener(),
            new BanScreener()
        ]);

        $message = $service->createMessage($roomId, Auth::id(), $request->message);

        return response()->json($message);
    }

    public function updateMessage(Request $request, $roomId): string|ChatMessage
    {
        $service = new MessageService($this->messageRepository, [
            new UserScreener(),
            new MuteScreener(),
            new BanScreener()
        ]);

        $message = $service->updateMessage(
            $request->messageId,
            Auth::id(),
            $request->message
        );

        return $message;
    }

    public function typingEntry($roomId): JsonResponse
    {
        $service = new MessageService($this->messageRepository, []);
        $service->dispatchUserIsTyping($roomId, Auth::id());

        return response()->json("success");
    }
}
