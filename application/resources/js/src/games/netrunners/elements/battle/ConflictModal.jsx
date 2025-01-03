import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {Enemy} from "./Enemy.jsx";
import {Fight} from "./Fight.jsx";
import {Hide} from "./Hide.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";
import {Outcome} from "./Outcome.jsx";
import {BattleDetails} from "./BattleDetails.jsx";

export const ConflictModal = () => {

    const phaseKey = useNetrunnersStore(state => state.situation.phase.key);
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

    const fight = (hide = false) => {
        if (yourTurn) {
            submitMove({fight: !hide}, gamePlayId , setMessage, phaseKey);
        }
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
            <div className='flex grid grid-rows-4 gap-0 w-full h-[65.5vh] bg-neutral-800/95 rounded-b-[2vh]'>

                <div className='p-[4%]'>
                    <Enemy enemyKey={encounter.key} power={encounter.power} />
                </div>

                {phaseKey === 'conflict' && <div className='p-[4%]'>
                    {displayHide && <Hide onClick={() => fight(true)} />}
                </div>}

                {phaseKey === 'conflict' && <div className='p-[4%] row-span-2'>
                    <Fight yourTurn={yourTurn} onClick={() => fight()}/>
                </div>}

                {phaseKey === 'battle' && <div className='px-[4%] row-span-2'>
                    <BattleDetails player={activePlayer} />
                </div>}

                {phaseKey === 'battle' && <div className='p-[4%]'>
                    <Outcome player={activePlayer} />
                </div>}

            </div>

        </div>
    );
}
