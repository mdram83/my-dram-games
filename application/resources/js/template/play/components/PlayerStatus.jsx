import React from "react";

export const PlayerStatus = ({isCurrent, borderColorClass, avatarElement, name, isConnected = false}) => {

    const isYou = (name === window.MyDramGames.player.name);
    const gridColClass = 'flex items-center justify-start sm:justify-center col-span-2 sm:col-span-1 py-1 sm:py-0 sm:text-center';
    const circleClass = 'flex items-center justify-center w-7 sm:w-11 h-7 sm:h-11 border-3 border-solid ' + borderColorClass + ' rounded-full bg-white' + (isCurrent ? ' animate-pulse' : '');

    return (
        <div className={gridColClass}>

            <div className={circleClass}>
                {avatarElement}
            </div>

            <div className="ml-2 sm:ml-3">
                <span>{name}</span>
                {isYou && <span className="pl-0.5 sm:pl-1 font-normal text-sm sm:text-lg"> (You)</span>}
            </div>

            {
                !isConnected &&
                <div className="ml-3 sm:ml-4 pb-0 sm:pb-2">
                    <i className="fa fa-wifi text-xs sm:text-md text-red-600 animate-ping"></i>
                </div>
            }

        </div>
    );

}
