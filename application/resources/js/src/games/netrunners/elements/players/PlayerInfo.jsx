import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {CharacterDetails} from "../character/CharactedDetails.jsx";
import {PlayerDetails} from "./PlayerDetails.jsx";

export const PlayerInfo = () => {

    const display = useNetrunnersStore(state => state.playerInfoScreen.display);
    const playerName = useNetrunnersStore(state => state.playerInfoScreen.playerName);
    const characterPriority = useNetrunnersStore(state => state.playerInfoScreen.characterPriority);
    const setPlayerInfoScreen = useNetrunnersStore(state => state.setPlayerInfoScreen);

    const classDivContainer = ' h-[70vh] w-[50vh] sm:w-[100vh] mt-[2vh] shadow-xl shadow-black text-white '
        + ' border-[0.3vh] sm:border-[0.25vh] border-solid border-fuchsia-500 rounded-[2vh] ';

    const classDivMobileDetailsCommon = ' p-[4%] w-[92%] h-[95%] ';

    const classDivMobileTabCommon = ' flex justify-center ';
    const classDivMobileTabActive = ' bg-neutral-800/95 rounded-t-[1vh] ';
    const classDivMobileTabInactive = ' bg-neutral-900/95 text-neutral-500 ';

    const classDivMobileTabPlayer = classDivMobileTabCommon
        + (characterPriority ? classDivMobileTabInactive : classDivMobileTabActive);
    const classDivMobileTabCharacter = classDivMobileTabCommon
        + (!characterPriority ? classDivMobileTabInactive : classDivMobileTabActive);

    const onClickMobileTab = (setCharacterTab = false) => {
        setPlayerInfoScreen(true, playerName, setCharacterTab);
    }

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
                             onClick={() => setPlayerInfoScreen(false)}
                        >
                            {'\u2715'}
                        </div>

                    </div>


                    {/*MOBILE INFO SCREEN SWITCH*/}
                    <div className='flex sm:hidden grid grid-cols-2 gap-0 bg-neutral-900/5 w-full content-end leading-[5.5vh] tracking-widest font-sans font-semibold text-[2.2vh] uppercase'>

                        {/*CHARACTER INFO SELECTION*/}
                        <div className={classDivMobileTabCharacter}
                             onClick={() => onClickMobileTab(true)}
                        >
                            Character
                        </div>

                        {/*PLAYER INFO SELECTION*/}
                        <div className={classDivMobileTabPlayer}
                             onClick={() => onClickMobileTab()}
                        >
                            Player
                        </div>

                    </div>

                    {/*DETAILS SECTION*/}
                    <div className='flex grid grid-cols-1 sm:grid-cols-2 gap-0 w-full h-[60vh] sm:h-[65.5vh] bg-neutral-800/95 rounded-b-[2vh]'>

                        {/*CHARACTER DETAILS SECTION*/}
                        <div className={classDivMobileDetailsCommon + (characterPriority ? ' block ' : ' hidden ') + ' sm:block '}>
                            <CharacterDetails playerName={playerName}/>
                        </div>

                        {/*PLAYER DETAILS SECTION*/}
                        <div
                            className={classDivMobileDetailsCommon + (characterPriority ? ' hidden ' : ' block ') + ' sm:block '}>
                            <PlayerDetails playerName={playerName}/>
                        </div>

                    </div>

                </div>
            }
        </>
    );
}
