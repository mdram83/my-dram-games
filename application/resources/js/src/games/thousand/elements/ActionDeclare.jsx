import React from "react";
import axios from "axios";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";

export const ActionDeclare = ({phase, declaration, disabled = false}) => {

    console.log('ActionDeclare');

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const label = declaration === 0 ? 'BOMB' : 'CONFIRM';

    const buttonClassName =
        ' border-0 rounded-md p-[1vh] w-[100%] font-sans font-bold text-white text-[2.2vh] '
        + (disabled ? ' bg-gray-600 opacity-80 ' : '  hover:cursor-pointer bg-blue-700 hover:bg-blue-900 ');

    const action = () => {
        console.log(declaration);
        axios
            .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {
                move: {data: {declaration: parseInt(declaration)}, phase: phase}
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
