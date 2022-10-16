

export const Message = (props) => {
    return (
      <div className="w-full">
          <span className="w-1/6" style={{padding: "5px"}}>
              {props.message.user.name}:
          </span>
          <span className="w-5/6">
              {props.message.message}
          </span>
      </div>
    );
}
