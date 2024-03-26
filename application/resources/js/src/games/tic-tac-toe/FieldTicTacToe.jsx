import React from "react";
import axios from "axios";
import {useTicTacToeStore} from "./useTicTacToeStore.jsx";

export const FieldTicTacToe = ({fieldKey, fieldValue}) => {

    const gamePlayId = useTicTacToeStore((state) => state.gamePlayId);

    const fieldBaseClass =
        ' w-[16vh] sm:w-[17.5vh] h-[16vh] sm:h-[17.5vh] flex justify-center items-center font-semibold text-[8vh] text-neutral-700 '
        + (fieldValue === null ? ' hover:cursor-pointer ' : ' hover:cursor-default ');

    const borderCombinedClass = (key) => {
        const horizontalBorder = ((key <= 6) ? ' border-b-[0.75vh] ' : ' border-b-0 ') + ' border-t-0 ';
        const verticalBorder = ((key % 3 > 0) ? ' border-r-[0.75vh] ' : ' border-r-0 ') + ' border-l-0 ';
        return horizontalBorder + verticalBorder + ' border-solid border-neutral-500 ';
    }

    const makeMove = () => {
        if (fieldValue === null) {
            // TODO handle double clicking so you only send move once (some flag, state or else)
            axios
                .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {move: {fieldKey: fieldKey}})
                .then(response => {
                    console.log(response);
                })
                .catch(error => {
                    console.log(error.response.data.message);
                    // TODO handle error messages
                    // setErrorMessage(error.response.data.message.message ?? 'Unexpected error');
                });
        }
    }

    return (
        <div className={fieldBaseClass + borderCombinedClass(fieldKey)} onClick={makeMove}>
            {fieldValue ?? ''}
        </div>
    );
}
