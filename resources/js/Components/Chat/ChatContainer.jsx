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
            editedMessage: undefined,
            sendTypingRequests: true,
        }
    }

    sendUserIsTypingRequest = () => {
        if (!this.state.sendTypingRequests)
            return;

        this.setState({sendTypingRequests:false});
        setTimeout(() => {
            this.setState({sendTypingRequests: true});
        }, 5000);

        axios({
            method: "post",
            url: `http://localhost:8000/chat/room/${this.props.currentChatRoom.id}/typing`
        }).then(response => {
            console.log(response.data);
        });
    }

    getMessages = () => {
        if (this.props.currentChatRoom.id && this.props.rerender) {
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
            this.props.setRerender(true);
            console.log('messagesent caught');
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
                <div id="user-is-typing-container">
                    {
                        this.props.typingUsers.map((userName) => {
                            return (
                                <p key={userName}>{userName} is typing...</p>
                            );
                        })
                    }
                </div>
                {
                    this.props.currentChatRoom.isMuted
                        ? <MutedComponent />
                        : this.state.editMessage
                            ? <EditMessage
                                message={this.state.editedMessage}
                                toggleEditMessage={this.toggleEditMessage.bind(this)}
                                currentChatRoom={this.props.currentChatRoom}
                                sendUserIsTypingRequest={this.sendUserIsTypingRequest.bind(this)}
                            />
                            : <MessageInput
                                currentChatRoom={this.props.currentChatRoom}
                                sendUserIsTypingRequest={this.sendUserIsTypingRequest.bind(this)}
                            />
                }
            </div>
        );
    }
}
