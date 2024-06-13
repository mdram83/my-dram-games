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

    console.log(pointsByRoundAndSeat);
    console.log(pointsByRoundAndSeat
        .filter((row, index) => (index + 1) >= (round - (displayNumberOfRounds - 1))));

    const renderPoints = () => {

        const headerRow = playersBySeat.map((playerName) =>
            <th key={playerName}>
                {playerName}
            </th>
        );

        headerRow.unshift(
            <th key='Round'>
                Round
            </th>
        );

        const pointsRows = pointsByRoundAndSeat.map((row, index) => {

                const pointsRow = row.map((points, seat) =>
                    <td key={seat}>
                        {points}
                    </td>
                );

                pointsRow.unshift(
                    <td key='round'>
                        {index + 1}
                    </td>
                );

                return (
                    <tr key={index + 1}>
                        {pointsRow}
                    </tr>
                );

            }).filter((row, index) => (index + 1) >= (round - (displayNumberOfRounds - 1)));

        return (
            <table>
                <thead>
                    <tr>
                        {headerRow}
                    </tr>
                </thead>
                <tbody>
                    {pointsRows}
                </tbody>
            </table>
        );
    }

    // TODO draw the table with names and points per round
    // show only last 5 rounds (or all if there is not more than 5)


    return (
        <div>
            {renderPoints()}
        </div>
    );
}
