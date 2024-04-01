import React from "react";
import {configTicTacToe} from "./configTicTacToe.jsx";

const GameRecords = (gameRecords) => {

    const winnerRecord = Object.values(gameRecords).filter((record) => record.isWinner === true)[0];
    const winnerName = winnerRecord.player;
    const winnerCharacterAvatar = configTicTacToe[winnerRecord.score.character].avatar;


    return (
        <>
            <i className='fa fa-trophy text-amber-500'></i>
            <span> {winnerName} ({winnerCharacterAvatar})</span>
        </>
    );
}

export default GameRecords;
