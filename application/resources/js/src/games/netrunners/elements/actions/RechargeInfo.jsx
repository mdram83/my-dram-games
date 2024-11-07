import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {ActionButton} from "./ActionButton.jsx";
import {Battery} from "../misc/Battery.jsx";

export const RechargeInfo = () => {

    console.log('RechargeInfo');

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const display = useNetrunnersStore(state => state.rechargeInfoScreen.display);
    const setRechargeInfoScreen = useNetrunnersStore(state => state.setRechargeInfoScreen);

    const playerName = MyDramGames.player.name;

    const onClick = () => {
        submitMove({recharge: true}, gamePlayId , setMessage, 'recharge');
        setRechargeInfoScreen(false);
    }

    const classDivContainer = ' h-[70vh] w-[50vh] sm:w-[100vh] mt-[2vh] shadow-xl shadow-black text-white '
        + ' border-[0.3vh] sm:border-[0.25vh] border-solid border-fuchsia-500 rounded-[2vh] ';
    const classDivDetails = ' p-[4%] w-[92%] h-[95%] ';
    const classDivSpan = ' col-span-2 sm:col-span-1 ';

    return (
        <>
            {
                display &&
                <div className={classDivContainer}>

                    {/*TOP BAR WITH PLAYER NAME AND CLOSE BUTTON*/}
                    <div className='flex bg-neutral-900 rounded-t-[2vh] text-[2vh] font-mono'>

                        {/*PLAYER NAME*/}
                        <div className='grow p-[1vh] text-lime-500'>
                            {playerName}@NETRUNNERS:/$
                        </div>

                        {/*CLOSE BUTTON*/}
                        <div className='w-[4.5vh] h-[4.5vh] flex items-center justify-center hover:bg-fuchsia-500 hover:cursor-pointer hover:rounded-tr-[1.6vh]'
                             onClick={() => setRechargeInfoScreen(false)}
                        >
                            {'\u2715'}
                        </div>

                    </div>


                    {/*DETAILS SECTION*/}
                    <div className='flex grid grid-cols-2 gap-0 w-full h-[65.5vh] bg-neutral-800/95 rounded-b-[2vh]'>

                        {/*BATTERY SECTION*/}
                        <div className={classDivDetails + classDivSpan}>
                            <div className=' flex justify-center items-center w-full h-full '>
                                <Battery points={3} />
                            </div>
                        </div>

                        {/*HACK SECTION*/}
                        <div className={classDivDetails + classDivSpan}>
                            <div className=' flex justify-center items-center w-full h-full '>
                                <div>Hack Animation</div>
                            </div>
                        </div>

                        {/*BUTTON SECTION*/}
                        <div className={classDivDetails + ' col-span-2 '}>
                            <div className=' flex justify-center items-center w-full h-full '>
                                <ActionButton onClick={onClick} label='Recharge' />
                            </div>
                        </div>

                    </div>

                </div>
            }
        </>
    );
}
