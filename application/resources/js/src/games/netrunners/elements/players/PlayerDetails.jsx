import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";

export const PlayerDetails = ({playerName}) => {

    console.log('player/PlayerDetails', playerName);

    const classDivCollectedPoints = ' flex items-center justify-center h-[20%] text-[5vh] font-mono uppercase ';

    return (
        <div className='size-full'>

            <div className='flex items-end justify-center bg-top bg-no-repeat bg-cover w-[100%] h-[80%] rounded-[1vh]'>

                <div className='w-[98%] px-[1%] sm:w-[96%] sm:px-[2%] leading-none bg-neutral-900/90 rounded-b-[0.8vh] text-lime-500 text-[2vh] sm:text-xs font-mono'>
                    Temp, will be adjusted
                </div>

            </div>

            <div className={classDivCollectedPoints}>
                5 x P
            </div>

        </div>
    );
}
