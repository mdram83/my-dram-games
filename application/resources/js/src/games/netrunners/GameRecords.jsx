import React from "react";

const GameRecords = (gameRecords) => {

    const renderRecords = () => Object.values(gameRecords).map((record) =>
        <div key={record.player}>
            <span> {record.player} </span>
            {record.isWinner && <i className='fa fa-trophy text-amber-500'></i>}
        </div>
    );

    return <>{renderRecords()}</>;
}

export default GameRecords;
