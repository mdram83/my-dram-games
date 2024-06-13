import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";

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

    const gridClassName = ` grid grid-cols-${(players.length + 1)} gap-0 `;
    const headerCommonClassName = ' border-0 border-b-[0.2vh] border-solid border-gray-600 flex items-center justify-center font-sans font-semibold text-[1.4vh] sm:text-[2vh] text-wrap '

    const renderPlayers = () => {

        const headerRow = playersBySeat.map((playerName) =>
            <div key={playerName} className={headerCommonClassName}>
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

    const renderPoints = pointsByRoundAndSeat
        .map((row, index) => {

            const pointsRow = row.map((points, seat) =>
                <div key={seat}>
                    {points}
                </div>
            );

            pointsRow.unshift(
                <div key='round' className={(index + 1) === round ? 'font-bold' : ''}>
                    {index + 1}
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
