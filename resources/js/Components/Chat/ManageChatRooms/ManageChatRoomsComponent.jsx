import {useEffect, useState} from "react";
import {AddUserToChatRoom} from "@/Components/Chat/ManageChatRooms/AddUserToChatRoom";
import {UserCard} from "@/Components/Chat/ManageChatRooms/UserCard";

export const ManageChatRoomsComponent = (props) => {
    const [editedRoom, setEditedRoom] = useState(props.chatRooms[0]);
    const [editedRoomUsers, setEditedRoomUsers] = useState({users: [], relationships: []});

    const getRoomUsers = () => {
        axios({
            method: "get",
            url: "http://localhost:8000/chat/room/" + editedRoom.id + "/user"
        })
            .then((response) => {
                setEditedRoomUsers(response.data);
            })
    }

    useEffect(() => {
        getRoomUsers();
    }, [editedRoom]);

    return (
        <div>
            <button className="bg-amber-500 px-4 rounded"
                onClick={() => {
                    props.toggleCreateChatRoomView(false);
                    props.toggleManageChatRooms(false);
                }}
                style={{fontSize: "2em"}}
            >
                &#8592;
            </button>
            <div
                onClick={() => {
                    props.toggleCreateChatRoomView(true);
                    props.toggleManageChatRooms(false);
                }}
            >
                New chatroom
            </div>
            <br/>
            <p>Public rooms:</p>
            <div className="flex flex-col">
                {
                    props.chatRooms.map((room) => {
                        if (room.is_private === 0 && room.is_mod === 1) {
                            return (
                                <div
                                    key={room.id}
                                    onClick={() => {
                                        setEditedRoom(room);
                                    }}
                                >
                                    {room.name}
                                </div>
                            );
                        } else {
                            return null;
                        }
                    })
                }
            </div>
            <br/>
            <p>Private rooms:</p>
            <div className="flex flex-col">
                {
                    props.chatRooms.map((room) => {
                        if (room.is_private === 1 && room.is_mod === 1) {
                            return (
                                <div
                                    key={room.id}
                                    onClick={() => {
                                        setEditedRoom(room);
                                    }}
                                >
                                    {room.name}
                                </div>
                            );
                        } else {
                            return null;
                        }
                    })
                }
            </div>
            <br/>
            <br/>
            <div>
                <p><strong>Name:</strong> {editedRoom.name}</p>
                <p><strong>Public/private:</strong> {editedRoom.is_private ? "Private" : "Public"}</p>
                <p><strong>Users:</strong></p>
                {
                    editedRoomUsers.users.map((user) => {
                        const relationship = editedRoomUsers.relationships.filter((x) => x.user_id === user.id)[0];
                        return (
                            <UserCard
                                key={user.email + relationship.is_muted + relationship.is_banned}
                                user={user}
                                room={editedRoom}
                                muted={relationship.is_muted}
                                banned={relationship.is_banned}
                            />
                        );
                    })
                }
            </div>
            <br/>
            <br/>
            <AddUserToChatRoom room={editedRoom}/>
        </div>
    );
}
