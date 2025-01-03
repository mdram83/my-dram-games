import React from "react";
import {submitMove} from "../../submitMove.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {Battery} from "../misc/Battery.jsx";
import {ActionButton} from "./ActionButton.jsx";

export const RestartInfo = () => {

    const isPhaseRestart = useNetrunnersStore(state => state.isPhaseRestart);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);

    const classDivContainer =
        ' h-[70vh] w-[50vh] mt-[2vh] shadow-xl shadow-black text-white '
        + ' border-[0.3vh] sm:border-[0.25vh] border-solid border-fuchsia-500 rounded-[2vh] ';

    const restart = () => {
        if (yourTurn) {
            submitMove({restart: true}, gamePlayId , setMessage, 'restart');
        }
    }

    const classDivDetails = ' p-[4%] ';

    return (
        isPhaseRestart &&
        (<div className={classDivContainer}>

            {/*TOP BAR WITH STATUS INFO*/}
            <div className='flex bg-neutral-900 rounded-t-[2vh] text-[2vh] font-mono'>
                <div className='grow p-[1vh] text-lime-500 uppercase flex justify-center'>
                    Restart...
                </div>
            </div>

            {/*DETAILS SECTION*/}
            <div className='flex grid grid-rows-3 gap-0 w-full h-[65.5vh] bg-neutral-800/95 rounded-b-[2vh]'>

                {/*BATTERY SECTION*/}
                <div className={classDivDetails}>
                    <div className=' flex justify-center items-center w-full h-full '>
                        <Battery points={0} targetPoints={1} />
                    </div>
                </div>

                {/*EMPTY SECTION*/}
                <div className={classDivDetails}></div>

                {/*BUTTON SECTION*/}
                <div className={classDivDetails}>
                    <div className=' flex justify-center items-center w-full h-full '>
                        {yourTurn && <ActionButton onClick={restart} label='Restart' />}
                    </div>
                </div>

            </div>

        </div>)
    );
}
