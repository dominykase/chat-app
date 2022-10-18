import {useState} from "react";

export const AddUserToChatRoom = () => {
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

    const handleChange = (e) => {
        setSearchInputValue(e.target.value);

        clearTimeout(timeout);
        timeout = setTimeout(() => {
            searchUser();
        }, 500);
    }

    return (
        <div>
            <p><strong>Add user:</strong></p>
            <input
                value={searchInputValue}
                onChange={handleChange}
                placeholder="Search by name or email"
                />
            <div className="flex flex-col">
                {
                    foundUsers.map((user) => {
                        return (
                            <div
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
