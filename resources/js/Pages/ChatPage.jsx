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
            rerender: true
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
                const event = new Event('messagesent');
                document.dispatchEvent(event);
            });
    }

    disconnect = (room) => {
        window.Echo.leave('chat.' + room.id);
    }

    updateChatRoom = (room) => {
        this.setState({currentChatRoom: room})
    }

    componentDidMount() {
        this.getChatRooms();

        document.addEventListener('chatroomcreated', () => {this.getChatRooms()});
    }

    componentDidUpdate(prevProps, prevState) {
        document.dispatchEvent(new Event('chatroomchanged'));

        this.disconnect(prevState.currentChatRoom);
        this.connect();
    }

    setRerender(toggle) {
        this.setState({rerender: toggle});
    }

    render() {
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
                        />

                }
            </div>
        );
    }
}
