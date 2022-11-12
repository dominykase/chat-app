

export const Message = (props) => {


    return (
      <div className="w-full">
          <span className="w-1/6" style={{padding: "5px"}}>
              {props.message.user.name}:
          </span>
          <span className="w-5/6">
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
