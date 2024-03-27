import React, {useState} from "react";
import axios from "axios";
import {useTicTacToeStore} from "./useTicTacToeStore.jsx";

export const FieldTicTacToe = ({fieldKey, fieldValue}) => {

    const gamePlayId = useTicTacToeStore((state) => state.gamePlayId);
    const moving = useTicTacToeStore((state) => state.moving);
    const setMoving = useTicTacToeStore((state) => state.setMoving);
    const setMessage = useTicTacToeStore((state) => state.setMessage);
    const [localMove, setLocalMove] = useState(false);

    const setMove = (status) => {
        setMoving(status);
        setLocalMove(status);
    }

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
            if (moving) return;
            setMove(true);
            axios
                .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {move: {fieldKey: fieldKey}})
                .then(() => setMove(false))
                .catch(error => {
                    setMessage(error.response.data.message ?? 'Unexpected error', true);
                    setMove(false);
                });
        }
    }

    return (
        <div className={fieldBaseClass + borderCombinedClass(fieldKey)} onClick={makeMove}>
            {localMove ? <i className="fa fa-puzzle-piece animate-ping text-[3vh] text-neutral-400"></i> : (fieldValue ?? '')}
        </div>
    );
}
