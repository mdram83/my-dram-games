import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {Enemy} from "./Enemy.jsx";
import {Fight} from "./Fight.jsx";
import {Hide} from "./Hide.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";

export const ConflictModal = () => {

    console.log('ConflictModal');

    const activePlayer = useNetrunnersStore(state => state.situation.activePlayer);
    const coordinates = useNetrunnersStore(state => state.situation.players[activePlayer].coordinates);
    const encounter = useNetrunnersStore(state => state.situation.map[coordinates.row][coordinates.column].encounter);
    const canHide = useNetrunnersStore(state => state.situation.canHide);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);

    const displayHide = canHide && yourTurn;

    const classDivContainer =
        ' h-[70vh] w-[50vh] mt-[2vh] shadow-xl shadow-black text-white '
        + ' border-[0.3vh] sm:border-[0.25vh] border-solid border-fuchsia-500 rounded-[2vh] ';

    const classDice = displayHide ? ' row-span-1 ' : ' row-span-2 ';
    const classAddDice = displayHide ? ' ' : ' mb-[5vh] ';

    const fight = () => {
        if (yourTurn) {
            submitMove({fight: true}, gamePlayId , setMessage, 'conflict');
        }
    }

    const hide = () => {
        submitMove({fight: false}, gamePlayId , setMessage, 'conflict');
    }

    return (
        <div className={classDivContainer}>

            {/*TOP BAR WITH STATUS INFO*/}
            <div className='flex bg-neutral-900 rounded-t-[2vh] text-[2vh] font-mono'>
                <div className='grow p-[1vh] text-lime-500 uppercase flex justify-center'>
                    Battle Details
                </div>
            </div>

            {/*DETAILS SECTION*/}
            <div className='flex grid grid-rows-3 gap-0 w-full h-[65.5vh] bg-neutral-800/95 rounded-b-[2vh]'>

                <div className='p-[4%]'>
                    <Enemy enemyKey={encounter.key} power={encounter.power} />
                </div>

                {displayHide &&
                    <div className='p-[4%]'>
                        <Hide onClick={hide} />
                    </div>
                }

                <div className={` p-[4%] ${classDice}`}>
                    <Fight gamePlayId={gamePlayId} setMessage={setMessage} yourTurn={yourTurn} addClass={classAddDice} onClick={fight} />
                </div>

            </div>

        </div>
    );
}
