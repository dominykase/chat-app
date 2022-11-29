import {useState} from "react";
import axios from "axios";

export const EditMessage = (props) => {

    const [inputValue, setInputValue] = useState(props.message.message);

    const handleChange = (e) => {
        if (e.target.value === '') {
            props.toggleEditMessage(undefined);
        }
        setInputValue(e.target.value);
    }

    const handleEnter = (e) => {
        if (e.key == "Enter") {
            submitMessage();
        }
    }

    const submitMessage = () => {
        if (inputValue == "") {
            return;
        }
        setInputValue("");
        axios({
            method: 'post',
            url: 'http://localhost:8000/chat/rooms/' + props.currentChatRoom.id + '/message/update',
            data: {
                messageId: props.message.id,
                roomId: props.message.chat_room_id,
                userId: props.message.user_id,
                message: inputValue
            }
        })
            .then((response) => {
                props.toggleEditMessage(undefined);
            })
    }

    return (
        <div className="border-solid border-2 border-indigo-600 rounded-l justify-evenly shadow-xl">
            <input
                className="h-1/6 w-5/6 border-none outline-none"
                type="text"
                value={inputValue}
                onChange={handleChange}
                onKeyDown={handleEnter}
                placeholder="Say something..."
            />
            <button
                className="h-10 w-1/6 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800"
                onClick={submitMessage}
            >
                Edit
            </button>
        </div>
    )
}
