import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";

export const HackChange = ({playerName}) => {

    const isHacked = useNetrunnersStore(state => state.situation.players[playerName].isHacked);

    const style = {
        backgroundImage: configNetrunners.covers.hacked,
        opacity: 0.5,
        filter: 'brightness(200%)',
    };

    return isHacked && <div className='absolute size-full rounded-full bg-center bg-no-repeat bg-cover' style={style}></div>;
}
