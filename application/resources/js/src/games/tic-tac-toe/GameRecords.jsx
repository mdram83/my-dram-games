import React from "react";
import {configTicTacToe} from "./elements/configTicTacToe.jsx";

const GameRecords = (gameRecords) => {

    const winnerRecord = Object.values(gameRecords).filter((record) => record.isWinner === true)[0];
    const hasWinner = winnerRecord !== undefined;
    const winnerName = hasWinner ? winnerRecord.player : undefined;
    const winnerCharacterAvatar = hasWinner ? configTicTacToe[winnerRecord.score.character].avatar : undefined;

    return (
        <>
            {hasWinner &&
                <>
                    <i className='fa fa-trophy text-amber-500'></i>
                    <span> {winnerName} ({winnerCharacterAvatar})</span>
                </>
            }

            {!hasWinner && <span>Game finished with draw.</span>}
        </>
    );
}

export default GameRecords;
