# Documentation
## App\Services\Messages\MessageService

### MessageService::__construct
```
public function __construct(private MessageRepository $repository, private array $screens)
```
#### Description
Creates and returns an instance of `App\Services\Messages\MessageService`
#### Parameters
+ a `MessageRepository` instance (persistence layer)
+ an array of `ScreenInterface` objects
#### Returns
An instance of `MessageService`
<hr/>

### MessageService::getMessages
```
public function getMessages(int $roomId, int $userId): string|array
```
#### Description
Retrieves all messages in a chat room by given chat room ID.
#### Parameters
+ `int` ID of chat room to receive messages for
+ `int` ID of the authenticated user making the request through API (can be acquired by Laravel's `Auth::id()`)
#### Returns
+ `string` is returned if one of the `ScreenInterface` objects passed through constructor denies access to the request. Returned value is the resulting error message of the `ScreenInterface` object. For example, if a user, banned in this particular chat room, requests to get messages from this chat room they will see `User is banned in this chat room` message returned.
+ `array` of chat room messages is returned otherwise (array keys: `id`, `chat_room_id`, `user_id`, `message`, `created_at`, `updated_at`, `canEdit`).
<hr/>

### MessageService::createMessage
```
public function createMessage(int $roomId, int $userId, string $message): string|ChatMessage
```
#### Description
Creates a new message by user (of provided user ID) in the chat room (by given chat room ID).
#### Parameters
+ `int` ID of the chat room to create message in
+ `int` ID of the user that writes the message (must be the same as authenticated user's ID)
+ `string` message body
#### Returns
+ `string` is returned if one of the `ScreenInterface` objects passed through constructor denies access to the request. Returned value is the resulting error message of the `ScreenInterface` object. For example, if a user, muted in this particular chat room, attempts to post a message in this chat room they will see `User is muted in this chat room` message returned (in this case, message will not be created).
+ `ChatMessage` instance of the newly created message is returned otherwise
<hr/>

### MessageService::updateMessage
```
public function updateMessage(int $messageId, int $roomId, int $userId, int $authId, string $message): string|ChatMessage
```
#### Description
Updates an existing message given by messageID.
#### Parameters
+ `int` ID of message to be updated
+ `int` ID of room message is in
+ `int` ID of user that created the message
+ `int` ID of authenticated user making the request
+ `string` new message body
#### Returns
+ `string` is returned if one of the `ScreenInterface` objects passed through constructor denies access to the request. Returned value is the resulting error message of the `ScreenInterface` object. For example, if a user who is different from the user that created the message attempts to post a message in this chat room they will see `Message does not belong to this user` message returned (in this case, message will not be updated).
+ `ChatMessage` instance of the updated message
