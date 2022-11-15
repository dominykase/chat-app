import {ChatMessagesContainer} from "@/Components/Chat/ChatMessagesContainer";
import {MessageInput} from "@/Components/Chat/MessageInput";
import {Component, useEffect, useState} from "react";
import {MutedComponent} from "@/Components/Chat/MutedComponent";
import {EditMessage} from "@/Components/Chat/EditMessage";

export class ChatContainer extends Component {

    constructor(props) {
        super(props);

        this.state = {
            messages: [],
            editMessage: false,
            editedMessage: undefined
        }
    }

    getMessages = () => {
        if (this.props.currentChatRoom.id) {
            axios({
                method: "get",
                url: `http://localhost:8000/chat/room/${this.props.currentChatRoom.id}`
            })
                .then((response) => {
                    this.setState({messages: response.data})
                })
        }
    }

    toggleEditMessage = (message) => {
        this.setState((prevState, props) => ({
           editMessage: !prevState.editMessage,
           editedMessage: message
        }));
    }

    componentDidMount() {
        this.getMessages();

        document.addEventListener('messagesent', () => {
            this.getMessages();
        });

        document.addEventListener('chatroomchanged', () => {
            this.getMessages();
        });
    }

    render() {
        return (
            <div className="container px-4 h-screen w-5/6" id="chat_container">
                <ChatMessagesContainer
                    messages={this.state.messages}
                    toggleEditMessage={this.toggleEditMessage.bind(this)}
                />
                {
                    this.props.currentChatRoom.is_muted
                        ? <MutedComponent />
                        : this.state.editMessage
                            ? <EditMessage
                                message={this.state.editedMessage}
                                toggleEditMessage={this.toggleEditMessage.bind(this)}
                                currentChatRoom={this.props.currentChatRoom}
                            />
                            : <MessageInput currentChatRoom={this.props.currentChatRoom}/>
                }
            </div>
        );
    }
}
