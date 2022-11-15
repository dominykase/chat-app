import {useEffect, useState} from "react";
import {AddUserToChatRoom} from "@/Components/Chat/ManageChatRooms/AddUserToChatRoom";
import {UserCard} from "@/Components/Chat/ManageChatRooms/UserCard";

export const ManageChatRoomsComponent = (props) => {
    let room = props.chatRooms.filter((x) => x.is_mod == 1)[0];
    if (!room){
        room = null
    }
    console.log(props.chatRooms);
    const [editedRoom, setEditedRoom] = useState(room);
    const [editedRoomUsers, setEditedRoomUsers] = useState({users: [], relationships: []});

    const getRoomUsers = () => {
        if (!editedRoom) {
            return;
        }
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
        <div className="w-full flex flex-col">
            <div className="w-full flex flex-row">
                <button className="w-1/6 bg-amber-300"
                    onClick={() => {
                        props.toggleCreateChatRoomView(false);
                        props.toggleManageChatRooms(false);
                    }}
                    style={{fontSize: "2em"}}
                >
                    &#8592;
                </button>
                <div
                    className="w-5/6 bg-gray-100 hover:bg-gray-200 justify-center flex items-center cursor-pointer"
                    onClick={() => {
                        props.toggleCreateChatRoomView(true);
                        props.toggleManageChatRooms(false);
                    }}
                >
                    <p>New chatroom</p>
                </div>
            </div>
            <div className="w-full flex flex-row mt-4">
                <p><strong>Public rooms:</strong></p>
                <div className="flex flex-col w-1/2 h-60 overflow-scroll">
                    {
                        props.chatRooms.map((room) => {
                            if (room.is_private === 0 && room.is_mod === 1 && editedRoom) {
                                return (
                                    <div
                                        className="m-1 p-1 text-center"
                                        key={room.id}
                                        onClick={() => {
                                            setEditedRoom(room);
                                        }}
                                        style={{
                                            backgroundColor: editedRoom === room ? "lightblue" : "white",
                                            border: "1px solid lightblue"
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
                <p><strong>Private rooms:</strong></p>
                <div className="flex flex-col w-1/2 h-60 overflow-scroll">
                    {
                        props.chatRooms.map((room) => {
                            if (room.is_private === 1 && room.is_mod === 1 && editedRoom) {
                                return (
                                    <div
                                        className="m-1 p-1 text-center"
                                        key={room.id}
                                        onClick={() => {
                                            setEditedRoom(room);
                                        }}
                                        style={{
                                            backgroundColor: editedRoom === room ? "lightblue" : "white",
                                            border: "1px solid lightblue"
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
            </div>
            <div className="w-full flex flex-row mt-4">
                <div className="w-1/2">
                    <p><strong>Name:</strong> {editedRoom ? editedRoom.name : null}</p>
                    <p><strong>Public/private:</strong> {editedRoom && editedRoom.is_private ? "Private" : "Public"}</p>
                    <p><strong>Users:</strong></p>
                    <div className="w-full h-60 overflow-scroll">
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
                </div>
                <br/>
                <br/>
                <AddUserToChatRoom room={editedRoom}/>
            </div>
        </div>
    );
}
