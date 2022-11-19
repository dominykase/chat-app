import {ChatRoomBox} from "@/Components/Chat/ChatRoomBox";

export const ChatRoomSelection = (props) => {

    return (
        <div className="overflow-scroll w-1/6">
            <>
                <div
                    className="bg-gray-100 hover:bg-gray-200 text-center py-4 cursor-pointer"
                    onClick={() => props.toggleManageChatRooms(true)}
                >
                    Manage chat rooms
                </div>
                {
                    props.chatRooms &&
                    props.chatRooms.map((room) =>
                        room.is_banned
                            ? null
                            : (
                                <ChatRoomBox
                                    key={room.id}
                                    chatRoom={room}
                                    selected={props.currentChatRoom.id == room.id ? true : false}
                                    setChatRoom={props.setChatRoom}
                                    setRerender={props.setRerender}
                                />
                            )
                    )
                }
            </>
        </div>
    );
}
