import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";

export const Hacked = ({playerName, toBeHacked = undefined}) => {

    const isHacked = useNetrunnersStore(state => state.situation.players[playerName].isHacked);

    const classCommon = ' rounded-full ';
    const classImageDiv = ' size-full bg-cover bg-no-repeat bg-center ';
    let classBorderDiv = ' h-[80%] aspect-square border-[0.5vh] border-solid ';
    let styleApplyFilter = false;

    switch (true) {
        case (isHacked && toBeHacked === false):
            classBorderDiv += ' border-green-500 animate-pulse ';
            styleApplyFilter = true;
            break;
        case (isHacked && toBeHacked === true):
            classBorderDiv += ' border-red-500 animate-pulseFast ';
            styleApplyFilter = false;
            break;
        case (isHacked && toBeHacked === undefined):
            classBorderDiv += ' border-red-500 ';
            styleApplyFilter = false;
            break;
        case (!isHacked && toBeHacked === false):
            classBorderDiv += ' border-neutral-500 opacity-50 ';
            styleApplyFilter = true;
            break;
        case (!isHacked && toBeHacked === true):
            classBorderDiv += ' border-red-500 animate-pulseFast ';
            styleApplyFilter = false;
            break;
        case (!isHacked && toBeHacked === undefined):
            classBorderDiv += ' border-neutral-500 opacity-50 ';
            styleApplyFilter = true;
            break;
    }

    const styleImage = {
        backgroundImage: configNetrunners.covers.hacked,
        filter: (styleApplyFilter ? 'grayscale(100%) brightness(2)' : ''),
    }

    return (
        <div className='flex items-center justify-center size-full'>
            <div className={classBorderDiv + classCommon} >
                <div className={classImageDiv + classCommon} style={styleImage}></div>
            </div>
        </div>
    );
}
