import {ChatRoomBox} from "@/Components/Chat/ChatRoomBox";

export const ChatRoomSelection = (props) => {

    return (
        <div className="overflow-scroll w-1/6">
            <>
                <div
                    onClick={() => props.toggleManageChatRooms(true)}
                >
                    Manage chat rooms
                </div>
                {
                    props.chatRooms &&
                    props.chatRooms.map((room) => {
                        return (
                            <ChatRoomBox
                                key={room.id}
                                chatRoom={room}
                                selected={props.currentChatRoom.id == room.id ? true : false}
                                setChatRoom={props.setChatRoom}
                            />
                        );
                    })
                }
            </>
        </div>
    );
}
