import {useState} from "react";

export const AddUserToChatRoom = (props) => {
    const [searchInputValue, setSearchInputValue] = useState("");
    const [foundUsers, setFoundUsers] = useState([]);
    const [selectedUser, setSelectedUser] = useState(null);

    let timeout;

    const searchUser = () => {
        axios({
            method: "get",
            url: "http://localhost:8000/user/search?query=" + encodeURIComponent(searchInputValue)
        })
            .then((response) => {
                setFoundUsers(response.data);
            });
    }

    const addUser = () => {
        axios({
            method: "post",
            url: "http://localhost:8000/chat/room/" + props.room.id + "/user",
            data: {
                userId: selectedUser.id
            }
        })
            .then((response) => {
                console.log(response);
            })
    }

    const handleChange = (e) => {
        setSearchInputValue(e.target.value);

        if (searchInputValue.length === 0) {
            return;
        }

        clearTimeout(timeout);
        timeout = setTimeout(() => {
            searchUser();
        }, 500);
    }

    return (
        <div className="w-1/2">
            <p><strong>Add user:</strong></p>
            <input
                value={searchInputValue}
                onChange={handleChange}
                placeholder="Search by name or email"
                />
            <button
                className="px-4 rounded bg-amber-300 ml-2"
                onClick={addUser}
            >
                Add user
            </button>
            <div className="flex flex-col h-60 mt-6 overflow-scroll">
                {
                    foundUsers.map((user) => {
                        return (

                            <div
                                key={user.name + user.email}
                                style={{
                                    backgroundColor: selectedUser === user ? "blue" : "white"
                                }}
                                onClick={() => {
                                    setSelectedUser(user);
                                }}
                            >
                                {user.name} {user.email}
                            </div>

                        );
                    })
                }
            </div>
        </div>
    );
}
