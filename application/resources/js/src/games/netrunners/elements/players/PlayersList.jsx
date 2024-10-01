import React from 'react';
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {Avatar} from "./Avatar.jsx";

export const PlayersList = () => {

    const players = useNetrunnersStore(state => state.situation.players);
    const names = Object.getOwnPropertyNames(players);
    const numberOfPlayers = names.length;
    const seats = new Array(numberOfPlayers);

    names.forEach((name) => {
        seats[players[name].seat - 1] = {
            seat: players[name].seat,
            name: name,
            character: players[name].character,
        };
    });

    // TODO next make a Grid...
    // TODO next make Avatar clickable (open Player/Character view)...

    const listItems = seats.map(seat =>
        <div className='border border-solid border-green-600 mt-0 sm:mt-4' key={seat.seat}>
            <Avatar playerName={seat.name} character={seat.character ?? undefined} />
        </div>
    );

    const classGrid = ' grid grid-cols-' + numberOfPlayers + ' sm:grid-cols-1 ';
    const classGap = ' gap-' + (6 - numberOfPlayers) + ' sm:gap-2 ';
    const classDiv = classGrid + classGap + ' flex items-center justify-items-center ';

    return (
        <div className={classDiv}>
            {listItems}
        </div>
    );
}
