
export const ChatRoomBox = (props) => {

    return (
        <div
            className="text-center mx-2 my-2 rounded-lg"
            style={{
                backgroundColor: props.selected ? "lightblue" : "white",
                border: "1px solid lightblue"
            }}
            onClick={() => props.setChatRoom(props.chatRoom)}
        >
            <span>{props.chatRoom.name}</span>
        </div>
    );
}
