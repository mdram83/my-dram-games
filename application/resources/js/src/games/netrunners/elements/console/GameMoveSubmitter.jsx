import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";

export const GameMoveSubmitter = () => {

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const moveData = useNetrunnersStore(state => state.moveData);
    const resetMoveData = useNetrunnersStore(state => state.resetMoveData);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);
    const setPlayerInfoScreen = useNetrunnersStore(state => state.setPlayerInfoScreen);

    const active = yourTurn && moveData.payload !== {} && moveData.phase !== null;

    const onClick = () => {
        submitMove(moveData.payload, gamePlayId, setMessage, moveData.phase);
        setPlayerInfoScreen(false);
        resetMoveData();
    }

    const classButtonDiv
        = ' w-[25vh] h-[6vh] flex justify-center items-center '
        + ' border border-solid border-[0.5vh] rounded-[1.5vh] border-orange-500 bg-neutral-200 hover:bg-neutral-300 '
        + ' shadow-actionSm hover:shadow-actionLg cursor-pointer '
        + ' font-sans font-bold antialiased text-[3vh] uppercase text-black ';

    return (
        <>{active &&
            <div className=' flex justify-center items-center w-full h-full bg-neutral-900/50 '>
                <div className={classButtonDiv} onClick={onClick}>
                    <div>{moveData.label ?? 'Continue'}</div>
                </div>
            </div>
        }</>
    );
}
