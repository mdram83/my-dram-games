import React from "react";
import axios from "axios";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";

export const ActionBid = ({decision, amount, phase, disabled = false}) => {

    console.log('  call ActionBid');

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const label = decision === 'bid' ? 'BID ' + amount : 'PASS';
    const buttonClassName =
        ' border-0 rounded-md p-[1vh] w-[100%] font-sans font-bold text-white '
        + (disabled ? ' bg-gray-600 opacity-80 ' : '  hover:cursor-pointer bg-blue-700 hover:bg-blue-900 ');

    const action = () => {
        axios
            .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {
                move: {data: {decision: decision, bidAmount: amount}, phase: phase}
            })
            .then(() => {})
            .catch(error => {
                console.log(error.response);
                setMessage(error.response.data.message ?? 'Unexpected error', true);
            });
    }

    return (
        <button className={buttonClassName} onClick={action} disabled={disabled}>
            {label}
        </button>
    );

}
