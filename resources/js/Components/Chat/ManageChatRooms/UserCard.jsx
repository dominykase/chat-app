import {useEffect, useState} from "react";

export const UserCard = (props) => {
    const [muteCheckBox, setMuteCheckBox] = useState(props.muted == 1 ? true : false);
    const [banCheckBox, setBanCheckBox] = useState(props.banned == 1 ? true : false);

    const updateUser = () => {
        axios({
            method: "post",
            url: "http://localhost:8000/chat/room/" + props.room.id + "/user/update",
            data: {
                userId: props.user.id,
                mute: muteCheckBox,
                ban: banCheckBox
            }
        })
            .then((response) => {
                console.log(response);
            })
    }

    return (
        <div className="flex flex-col">
            <div className="w-full">{props.user.name} {props.user.email}</div>
            <div className="w-full">
                Mute:
                <input
                    className="ml-2 mr-2"
                    type="checkbox"
                    checked={muteCheckBox}
                    onChange={() => {
                        setMuteCheckBox(!muteCheckBox);
                    }}
                />
                Ban:
                <input
                    className="ml-2 mr-2"
                    type="checkbox"
                    checked={banCheckBox}
                    onChange={() => {
                        setBanCheckBox(!banCheckBox);
                    }}
                />
                <button
                    className="bg-amber-300 px-4 rounded"
                    onClick={updateUser}
                    >Submit</button>
            </div>
        </div>
    );
}
