import { ChatContainer } from "@/Components/Chat/ChatContainer";
import {Component, useEffect, useRef, useState} from "react";
import {ChatRoomSelection} from "@/Components/Chat/ChatRoomSelection";
import {CreateChatRoomView} from "@/Components/Chat/ManageChatRooms/CreateChatRoomView";
import {ManageChatRoomsComponent} from "@/Components/Chat/ManageChatRooms/ManageChatRoomsComponent";

export default class ChatPage extends Component {
    constructor(props) {
        super(props);

        this.state = {
            chatRooms: [],
            createChatRoomView: false,
            manageChatRooms: false,
            currentChatRoom: {id: undefined},
            prevChatRoom: {id: undefined},
            rerender: true,
            typingUsers: []
        }

        window.Echo.channel("chatroomfeed")
            .listen('.chatrooms.updated', e => {
                this.getChatRoomsWithoutChangingCurrentRoom();
            });
    }

    toggleCreateChatRoomView = (toggle) => {
        this.setState({createChatRoomView: toggle});
    }

    toggleManageChatRooms = (toggle) => {
        this.setState({manageChatRooms: toggle});
    }

    getChatRooms = () => {
        axios({
            method: "get",
            url: "http://localhost:8000/chat/rooms"
        })
            .then((response) => {
                console.log(response.data);
                const displayedRooms = response.data.filter((room) => room.isBanned === 0);
                this.setState({
                    chatRooms: response.data,
                    currentChatRoom: displayedRooms[0],
                    rerender: true
                })

            })
    }

    getChatRoomsWithoutChangingCurrentRoom = () => {
        axios({
            method: "get",
            url: "http://localhost:8000/chat/rooms"
        })
            .then((response) => {
                this.setState({
                    chatRooms: response.data,
                    rerender: false
                })
            })
    }

    connect = () => {
        window.Echo.private("chat." + this.state.currentChatRoom.id)
            .listen('.message.new', e => {
                console.log(".message.new");
                const event = new Event('messagesent');
                document.dispatchEvent(event);
            })
            .listen('.user.typing', e => {
                if (this.state.typingUsers.length === 0) {
                    this.setState({typingUsers: [e.userName]});
                    setTimeout(() => {
                        this.setState({typingUsers: []});
                    }, 5000);
                }
            });
    }

    disconnect = (room) => {
        window.Echo.leave('chat.' + room.id);
    }

    updateChatRoom = (room) => {
        this.setState((prevState) => {
            return {
                prevChatRoom: prevState.currentChatRoom,
                currentChatRoom: room
            };
        })
    }

    componentDidMount() {
        this.getChatRooms();
        document.dispatchEvent(new Event('chatroomchanged'));

        document.addEventListener('chatroomcreated', () => {this.getChatRooms()});
        document.addEventListener('chatroomchanged', () => {
            this.disconnect(this.state.prevChatRoom);
            this.connect();
        })
    }

    componentDidUpdate(prevProps, prevState) {
        document.dispatchEvent(new Event('chatroomchanged'));
    }

    setRerender(toggle) {
        this.setState({rerender: toggle});
    }

    render() {
        console.log({prev: this.state.prevChatRoom.id, now: this.state.currentChatRoom.id});
        return (
            <div className="w-full h-full flex flex-row">
                <ChatRoomSelection
                    chatRooms={this.state.chatRooms}
                    currentChatRoom={this.state.currentChatRoom}
                    setChatRoom={this.updateChatRoom.bind(this)}
                    toggleCreateChatRoomView={this.toggleCreateChatRoomView.bind(this)}
                    toggleManageChatRooms={this.toggleManageChatRooms.bind(this)}
                    setRerender={this.setRerender.bind(this)}
                />
                {
                    this.state.manageChatRooms
                    ? <ManageChatRoomsComponent
                        toggleCreateChatRoomView={this.toggleCreateChatRoomView.bind(this)}
                        toggleManageChatRooms={this.toggleManageChatRooms.bind(this)}
                        chatRooms={this.state.chatRooms}
                    />
                    : this.state.createChatRoomView
                        ? <CreateChatRoomView
                            toggleCreateChatRoomView={this.toggleCreateChatRoomView.bind(this)}
                            toggleManageChatRooms={this.toggleManageChatRooms.bind(this)}
                        />
                        : <ChatContainer
                            chatRooms={this.state.chatRooms}
                            currentChatRoom={this.state.currentChatRoom}
                            rerender={this.state.rerender}
                            setRerender={this.setRerender.bind(this)}
                            typingUsers={this.state.typingUsers}
                        />

                }
            </div>
        );
    }
}
