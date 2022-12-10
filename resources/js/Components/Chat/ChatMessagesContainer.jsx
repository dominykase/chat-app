import {Message} from "@/Components/Chat/Message";


export const ChatMessagesContainer = (props) => {
    return (
        <div className="container h-5/6 p-2 flex flex-col-reverse overflow-scroll">
            {
                props.messages &&
                props.messages.map((message) => {
                    return (
                        <Message
                            key={message.id}
                            message={message}
                            toggleEditMessage={props.toggleEditMessage}
                        />
                    );
                })
            }
        </div>
    );
}
