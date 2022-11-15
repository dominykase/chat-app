

export const Message = (props) => {

    return (
      <div className="w-full m-2">
          <span className="w-1/6 p-1 bg-amber-400 rounded-lg">
              {props.message.user.name}:
          </span>
          <span className="w-5/6 pl-2">
              {props.message.message}
          </span>
          {
              props.message.canEdit === 1
              &&
              <button
                  onClick={() => {props.toggleEditMessage(props.message)}}
              >
                  &#9998;
              </button>
          }
      </div>
    );
}
