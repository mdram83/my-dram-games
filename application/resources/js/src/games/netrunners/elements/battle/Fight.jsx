import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";

export const Fight = ({onClick, yourTurn, addClass = ''}) => {

    const styleImage = {
        backgroundImage: configNetrunners.covers.diceM,
    };

    const classDiv = ' h-full max-h-[20vh] bg-center bg-no-repeat bg-cover aspect-square rounded-full border-[0.4vh] border-solid '
        + (yourTurn ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ' : ' border-cyan-500 shadow-actionSmOp ')
        + addClass;

    return (
        <div className='flex size-full justify-center items-center'>
            <div className={classDiv} style={styleImage} onClick={onClick}></div>
        </div>
    );
}
