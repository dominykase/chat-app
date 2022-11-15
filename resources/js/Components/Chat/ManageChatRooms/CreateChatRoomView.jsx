import {useFormik} from 'formik';

export const CreateChatRoomView = (props) => {

    const formik = useFormik({
        initialValues: {
            roomName: "",
            private: false,
        },
        onSubmit: (values) => {
            console.log(values);
            axios({
                method: 'post',
                url: 'http://localhost:8000/chat/room/create',
                data: values
            })
                .then((response) => {
                    document.dispatchEvent(new Event('chatroomcreated'));
                    props.toggleCreateChatRoomView(false);
                })
        }
    })

    return (
        <div>
            <>
            <button className="bg-amber-300 px-4"
                    onClick={() => {
                        props.toggleCreateChatRoomView(false);
                        props.toggleManageChatRooms(true);
                    }}
                    style={{fontSize: "2em"}}
            >
                &#8592;
            </button>
            <form onSubmit={formik.handleSubmit}>
                <div className="flex flex-row">
                    <div className="flex flex-col">
                        <label className="mt-2" htmlFor="roomName">Room name: </label>
                        <br/>
                        <label htmlFor="private">Make channel private: </label>
                    </div>
                    <div className="pl-2">
                        <input
                            type="text"
                            name="roomName"
                            value={formik.values.roomName}
                            onChange={formik.handleChange}
                        />
                        <br/>
                        <input
                            className="mt-4"
                            type="checkbox"
                            name="private"
                            checked={formik.values.private}
                            onChange={formik.handleChange}
                        />
                    </div>
                </div>
                <br/>
                <button
                    className="bg-amber-300 px-4 rounded"
                    type="submit"
                >Create room</button>
            </form>
            </>
        </div>
    );
}
