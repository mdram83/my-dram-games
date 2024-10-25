import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";

export const GameMoveSubmitter = () => {

    console.log('GameMoveSubmitter');

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const moveData = useNetrunnersStore(state => state.moveData);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);

    console.log(moveData);

    const onClick = () => {
        submitMove(moveData.payload, gamePlayId, setMessage, moveData.phase);
    }

    return (
        <>{yourTurn && <div onClick={onClick}>GameMoveSubmitter</div>}</>
    );
}
