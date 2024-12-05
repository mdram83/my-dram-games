import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";

export const Enemy = ({enemyKey, power}) => {

    console.log('Enemy');

    const styleImage = {
        backgroundImage: configNetrunners.encounters[enemyKey].imageM,
    };

    const classImage = ' aspect-square bg-top bg-no-repeat bg-cover rounded-lg ';
    const classPower = ' aspect-square flex items-center justify-center text-pink-600 font-sans font-semibold text-[10vh] ';

    return (
        <div className='flex size-full justify-between'>
            <div className={classImage} style={styleImage}></div>
            <div className={classPower}><div>{power}</div></div>
        </div>
    );
}
