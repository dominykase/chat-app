<?php

namespace Services\Messages;

use App\Models\ChatMessage;
use App\Models\RoomUserRelationship;
use App\Models\User;
use App\Repositories\Messages\MessageRepository;
use App\Services\Messages\MessageService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testRetrievesAllMessagesCorrectly()
    {
        // prepare database
        ChatMessage::create(['user_id' => 1, 'chat_room_id' => 1, 'message' => 'abc']);
        ChatMessage::create(['user_id' => 1, 'chat_room_id' => 1, 'message' => 'def']);
        User::create(['name' => 'admin', 'password' => 'abc', 'email' => 'admin@admin.com']);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);

        $service = new MessageService(new MessageRepository(), []);
        $messages = $service->getMessages(1, 1);

        $this->assertEquals('abc', $messages[0]['message']);
        $this->assertEquals('def', $messages[1]['message']);
    }

    public function testCreatesAMessageCorrectly()
    {
        // prepare database
        User::create(['name' => 'admin', 'password' => 'abc', 'email' => 'admin@admin.com',]);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);

        $service = new MessageService(new MessageRepository(), []);
        $message = $service->createMessage(1, 1, 'hello');

        $this->assertEquals('hello', $message->message);
    }

    public function testUpdatesUnreadMessageCountCorrectly()
    {
        // prepare database
        User::create(['name' => 'admin', 'password' => 'abc', 'email' => 'admin@admin.com']);
        User::create(['name' => 'test', 'password' => 'abc', 'email' => 'test@test.com']);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 2, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 0, 'unread_count' => 0]);

        $repository = new MessageRepository();
        $service = new MessageService($repository, []);
        $message = $service->createMessage(1, 1, 'hello');

        $updatedRelationship = $repository->getRelationship(1, 2);

        $this->assertEquals(1, $updatedRelationship->unread_count);
    }

    public function testResetsUnreadMessagesUponRetrievingAllMessagesCorrectly()
    {
        // prepare database
        User::create(['name' => 'admin', 'password' => 'abc', 'email' => 'admin@admin.com']);
        User::create(['name' => 'test', 'password' => 'abc', 'email' => 'test@test.com']);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 2, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 0, 'unread_count' => 1]);

        $repository = new MessageRepository();
        $service = new MessageService($repository, []);
        $service->getMessages(1, 2);

        $updatedRelationship = $repository->getRelationship(1, 2);

        $this->assertEquals(0, $updatedRelationship->unread_count);
    }

    public function testUpdatesAMessageCorrectly()
    {
        // prepare database
        User::create(['name' => 'admin', 'password' => 'abc', 'email' => 'admin@admin.com']);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);
        $message = ChatMessage::create(['user_id' => 1, 'chat_room_id' => 1, 'message' => 'hello']);


        $repository = new MessageRepository();
        $service = new MessageService($repository, []);
        $updatedMessage = $service->updateMessage($message->id, 1, 1,'hello!');

        $this->assertEquals('hello!', $updatedMessage->message);
    }
}
