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
            <button className="bg-amber-500 px-4 rounded"
                    onClick={() => {
                        props.toggleCreateChatRoomView(false);
                        props.toggleManageChatRooms(true);
                    }}
                    style={{fontSize: "2em"}}
            >
                &#8592;
            </button>
            <form onSubmit={formik.handleSubmit}>
                <label htmlFor="roomName">Room name </label>
                <input
                    type="text"
                    name="roomName"
                    value={formik.values.roomName}
                    onChange={formik.handleChange}
                />
                <br/>
                <label htmlFor="private">Make channel private </label>
                <input
                    type="checkbox"
                    name="private"
                    checked={formik.values.private}
                    onChange={formik.handleChange}
                />
                <br/>
                <button type="submit">Create room</button>
            </form>
            </>
        </div>
    );
}
