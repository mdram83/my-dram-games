import React from "react";

const GameRecords = (gameRecords) => {

    const winnerRecord = Object.values(gameRecords).filter((record) => record.isWinner === true)[0];
    const hasWinner = winnerRecord !== undefined;
    const winnerName = hasWinner ? winnerRecord.player : undefined;

    return (
        <>
            {hasWinner &&
                <>
                    <i className='fa fa-trophy text-amber-500'></i>
                    <span> {winnerName} </span>
                </>
            }

            {!hasWinner && <span>Game finished without choosing the winner</span>}
        </>
    );
}

export default GameRecords;
