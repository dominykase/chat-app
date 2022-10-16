import {ChatMessagesContainer} from "@/Components/Chat/ChatMessagesContainer";
import {MessageInput} from "@/Components/Chat/MessageInput";
import {Component, useEffect, useState} from "react";

export class ChatContainer extends Component {

    constructor(props) {
        super(props);

        this.state = {
            messages: []
        }
    }

    getMessages = () => {
        axios({
            method: "get",
            url: `http://localhost:8000/chat/room/${this.props.currentChatRoom.id}`
        })
            .then((response) => {
                this.setState({messages: response.data})
            })
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
            <div className="container px-4 h-screen w-5/6" style={{border: "1px solid black"}} id="chat_container">
                <ChatMessagesContainer messages={this.state.messages}/>
                <MessageInput currentChatRoom={this.props.currentChatRoom}/>
            </div>
        );
    }
}
