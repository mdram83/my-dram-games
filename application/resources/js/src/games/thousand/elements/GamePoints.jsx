import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {configThousand} from "../configThousand.jsx";

export const GamePoints = () => {

    console.log('GamePoints');

    const displayNumberOfRounds = 5;

    const round = useThousandStore(state => state.situation.round);
    const orderedPlayers = useThousandStore(state => state.situation.orderedPlayers);

    const players = Object.getOwnPropertyNames(orderedPlayers);
    const playersBySeat = [];
    players.forEach((playerName) => playersBySeat[orderedPlayers[playerName].seat - 1] = playerName);

    const pointsByRoundAndSeat = [];
    for (let i = 1; i <= round; i++) {
        pointsByRoundAndSeat[i - 1] = playersBySeat.map((playerName) => orderedPlayers[playerName].points[i] ?? null);
    }

    const gridClassName = players.length === 4 ? ' grid grid-cols-5 gap-0 ' : ' grid grid-cols-4 gap-0 ';
    const headerCommonClassName =
        ' border-0 border-b-[0.2vh] border-solid border-gray-600 '
        + ' px-[1vh] py-[1vh] '
        + ' flex items-center justify-start '
        + ' font-sans font-semibold text-[2vh] sm:text-[2.4vh] text-ellipsis overflow-hidden ';

    const renderPlayers = () => {

        const headerRow = playersBySeat.map((playerName, index) =>
            <div key={playerName} className={headerCommonClassName + configThousand[index + 1].playerTextClass}>
                {playerName}
            </div>
        );

        headerRow.unshift(
            <div key='Round' className={headerCommonClassName}>
                Round
            </div>
        );

        return headerRow;
    }

    const rowCommonClassName =
        ' border-0 border-b-[0.2vh] border-dashed border-gray-400 '
        + ' h-[4vh] '
        + ' flex items-center justify-center '
        + ' font-sans font-medium text-[2vh] sm:text-[2.4vh] '

    const renderPoints = pointsByRoundAndSeat
        .map((row, index) => {

            const pointsRound = index + 1;

            const pointsRow = row.map((points, seat) => {

                const isBomb = orderedPlayers[playersBySeat[seat]].bombRounds.includes(pointsRound);
                const displayPoints = isBomb ? '\ud83d\udca3' : points;

                return (
                    <div key={seat} className={rowCommonClassName}>
                        {displayPoints}
                    </div>
                );
            });

            const rowRoundClassName =
            (pointsRound === round ? ' font-black ' : ' ') + ' text-gray-600 ';

            pointsRow.unshift(
                <div key='round' className={rowRoundClassName + rowCommonClassName}>
                    {pointsRound}
                </div>
            );

            return pointsRow;

        })
        .filter((row, index) => (index + 1) >= (round - (displayNumberOfRounds - 1)));

    return (
        <div className={gridClassName}>
            {renderPlayers()}
            {renderPoints}
        </div>
    );
}
