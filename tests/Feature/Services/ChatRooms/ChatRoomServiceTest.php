<?php

namespace Services\ChatRooms;

use App\Models\ChatRoom;
use App\Models\RoomUserRelationship;
use App\Models\User;
use App\Repositories\ChatRoom\ChatRoomRepository;
use App\Services\ChatRooms\ChatRoomService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatRoomServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testServiceRetrievesChatRoomsCorrectly()
    {
        ChatRoom::create(['name' => 'room1', 'is_private' => 1]);
        ChatRoom::create(['name' => 'room2', 'is_private' => 1]);
        ChatRoom::create(['name' => 'room3', 'is_private' => 1]);
        RoomUserRelationship::create(['room_id' => 2, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 1, 'unread_count' => 0]);
        RoomUserRelationship::create(['room_id' => 3, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 1,
            'is_mod' => 1, 'unread_count' => 0]);

        $service = new ChatRoomService(new ChatRoomRepository(), []);
        $rooms = $service->getRooms(1);

        $this->assertEquals(2, count($rooms));
        $this->assertEquals('room2', $rooms[0]['name']);
        $this->assertEquals(0, $rooms[0]['is_banned']);
        $this->assertEquals('room3', $rooms[1]['name']);
        $this->assertEquals(1, $rooms[1]['is_banned']);
    }

    public function testServiceCreatesAChatRoomCorrectly()
    {
        $service = new ChatRoomService(new ChatRoomRepository(), []);
        $room = $service->createNewChatRoom('room1', 0, 1);

        $this->assertNotNull($room);
        $this->assertEquals('room1', $room->name);
        $this->assertEquals(0, $room->is_private);
    }

    public function testServiceCreatesRelationshipsWhenCreatingAPrivateRoomCorrectly()
    {
        // prepare database
        $user1 = User::create(['name' => 'admin', 'password' => 'abc', 'email' => 'admin@admin.com']);
        $user2 = User::create(['name' => 'test', 'password' => 'abc', 'email' => 'test@test.com']);

        $service = new ChatRoomService(new ChatRoomRepository(), []);
        $room = $service->createNewChatRoom('room1', 1, $user1->id);

        $this->assertNotNull(RoomUserRelationship::where('room_id', $room->id)
            ->where('user_id', $user1->id)->get()->first()
        );
        $this->assertNull(RoomUserRelationship::where('room_id', $room->id)
            ->where('user_id', $user2->id)->get()->first()
        );
    }

    public function testServiceCreatesRelationshipsWhenCreatingAPublicRoomCorrectly()
    {
        // prepare database
        $user1 = User::create(['name' => 'admin', 'password' => 'abc', 'email' => 'admin@admin.com']);
        $user2 = User::create(['name' => 'test', 'password' => 'abc', 'email' => 'test@test.com']);

        $service = new ChatRoomService(new ChatRoomRepository(), []);
        $room = $service->createNewChatRoom('room1', 0, $user1->id);

        $relationship1 = RoomUserRelationship::where('room_id', $room->id)
            ->where('user_id', $user1->id)->get()->first();
        $relationship2 = RoomUserRelationship::where('room_id', $room->id)
            ->where('user_id', $user2->id)->get()->first();

        $this->assertNotNull($relationship1);
        $this->assertEquals(1, $relationship1->is_mod);
        $this->assertEquals(0, $relationship2->is_mod);
    }

    public function testServiceRetrievesUsersByRoomIdCorrectly()
    {
        $user1 = User::create(['name' => 'admin', 'password' => 'abc', 'email' => 'admin@admin.com']);
        $user2 = User::create(['name' => 'test', 'password' => 'abc', 'email' => 'test@test.com']);
        $user3 = User::create(['name' => 'test2', 'password' => 'abc', 'email' => 'test2@test2.com']);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => $user1->id, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 0, 'unread_count' => 0]);
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => $user2->id, 'is_muted' => 0, 'is_banned' => 1,
            'is_mod' => 0, 'unread_count' => 0]);
        RoomUserRelationship::create(['room_id' => 2, 'user_id' => $user3->id, 'is_muted' => 0, 'is_banned' => 1,
            'is_mod' => 0, 'unread_count' => 0]);

        $service = new ChatRoomService(new ChatRoomRepository(), []);

        $usersAndRelationships = $service->getUsersByRoomId(1);
        $this->assertEquals(2, count($usersAndRelationships['users']));
        $this->assertEquals('admin', $usersAndRelationships['users'][0]['name']);
        $this->assertEquals('test', $usersAndRelationships['users'][1]['name']);
        $this->assertEquals(0, $usersAndRelationships['relationships'][0]['is_banned']);
        $this->assertEquals(1, $usersAndRelationships['relationships'][1]['is_banned']);
    }

    public function testServiceRefusesToAddUserToARoomIfTheRoomIsPublicCorrectly()
    {
        $room = ChatRoom::create(['name' => 'room1', 'is_private' => 0]);

        $service = new ChatRoomService(new ChatRoomRepository(), []);
        $this->assertEquals(
            'Room is public, cannot add users.',
            $service->addUserToChatRoom($room->id, 1)
        );
    }

    public function testServiceAddsUserToARoomIfTheRoomIsPrivateCorrectly()
    {
        $room = ChatRoom::create(['name' => 'room1', 'is_private' => 1]);

        $service = new ChatRoomService(new ChatRoomRepository(), []);
        $this->assertEquals(
            'Added or already exists.',
            $service->addUserToChatRoom($room->id, 1)
        );
        $this->assertNotNull(
            RoomUserRelationship::where('room_id', $room->id)
                ->where('user_id', 1)
                ->get()->first()
        );
    }

    public function testServiceReturnsCorrectResponseWhenAddingAUserIfTheUserIsAlreadyInThePrivateRoomCorrectly()
    {
        $room = ChatRoom::create(['name' => 'room1', 'is_private' => 1]);
        RoomUserRelationship::create(['room_id' => $room->id, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 0, 'unread_count' => 0]);

        $service = new ChatRoomService(new ChatRoomRepository(), []);
        $this->assertEquals(
            'Added or already exists.',
            $service->addUserToChatRoom($room->id, 1)
        );
    }

    public function testServiceUpdatesAUserRoomRelationshipCorrectly()
    {
        RoomUserRelationship::create(['room_id' => 1, 'user_id' => 1, 'is_muted' => 0, 'is_banned' => 0,
            'is_mod' => 0, 'unread_count' => 0]);
        $service = new ChatRoomService(new ChatRoomRepository(), []);

        $service->updateRoomUserStatus(1, 1, 1, 0);
        $relationship = RoomUserRelationship::where('room_id', 1)
            ->where('user_id', 1)
            ->get()->first();
        $this->assertEquals(1, $relationship->is_muted);
        $this->assertEquals(0, $relationship->is_banned);

        $service->updateRoomUserStatus(1, 1, 0, 1);
        $relationship = RoomUserRelationship::where('room_id', 1)
            ->where('user_id', 1)
            ->get()->first();
        $this->assertEquals(0, $relationship->is_muted);
        $this->assertEquals(1, $relationship->is_banned);
    }
}
