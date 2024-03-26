import React from "react";
import {usePlayersStatusStore} from "./usePlayersStatusStore.jsx";

export const PlayerStatus = ({isCurrent, borderColorClass, avatarElement, name}) => {

    const connected = usePlayersStatusStore((state) => state.players[name]);
    const isYou = (name === window.MyDramGames.player.name);

    const gridColClass = 'flex items-center justify-start sm:justify-center col-span-2 sm:col-span-1 sm:text-center ';
    const circleClass =
        ' flex items-center justify-center w-[6vh] sm:w-[7vh] h-[6vh] sm:h-[7vh] border-3 border-solid '
        + borderColorClass + ' rounded-full bg-gray-200'
        + (isCurrent ? ' animate-pulse ' : ' ');

    return (
        <div className={gridColClass}>

            <div className={circleClass}>
                {avatarElement}
            </div>

            <div className="ml-[1.5vh] sm:ml-[2vh]">
                <span>{name}</span>
                {isYou && <span className="pl-[1vh] font-normal text-[2vh] sm:text-[2.5vh]"> (You)</span>}
            </div>

            {
                !connected &&
                <div className="ml-[2vh] pb-0 sm:pb-[1vh]">
                    <i className="fa fa-wifi text-[1.6vh] text-red-600 animate-ping"></i>
                </div>
            }

        </div>
    );

}
