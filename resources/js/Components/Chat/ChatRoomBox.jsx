
export const ChatRoomBox = (props) => {

    return (
        <div
            className="text-center mx-2 my-2 rounded-lg"
            style={{
                backgroundColor: props.selected ? "lightblue" : "white",
                border: "1px solid lightblue"
            }}
            onClick={() => {
                props.setRerender(true);
                props.setChatRoom(props.chatRoom);
            }}
        >
            <span>{props.chatRoom.name} {
                props.chatRoom.unread_messages > 0
                ? <span className="bg-amber-300 px-1 rounded-2xl">{props.chatRoom.unread_messages}</span>
                : null
            }</span>
        </div>
    );
}
