
export const ChatRoomBox = (props) => {

    return (
        <div
            style={{backgroundColor: props.selected ? "blue" : "white"}}
            onClick={() => props.setChatRoom(props.chatRoom)}
        >
            <span>{props.chatRoom.name}</span>
        </div>
    );
}
