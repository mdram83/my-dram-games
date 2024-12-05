import React from "react";
import {ActionButton} from "../actions/ActionButton.jsx";

export const Hide = ({onClick}) => {

    console.log('Hide');

    return (
        <div className=' flex justify-center items-center w-full h-full '>
            <ActionButton onClick={onClick} label='Hide'/>
        </div>
    );
}
