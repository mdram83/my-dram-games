import React from "react";

export const ActionButton = ({onClick, label}) => {

    const classButtonDiv
        = ' w-[25vh] h-[6vh] flex justify-center items-center '
        + ' border border-solid border-[0.5vh] rounded-[1.5vh] border-orange-500 bg-neutral-200 hover:bg-neutral-300 '
        + ' shadow-actionSm hover:shadow-actionLg cursor-pointer '
        + ' font-sans font-bold antialiased text-[3vh] uppercase text-black ';

    return (
        <div className={classButtonDiv} onClick={onClick}>
            <div>{label}</div>
        </div>
    );
}
