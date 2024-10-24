import React from 'react';
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PlayerAvatar} from "./PlayerAvatar.jsx";

export const PlayersList = () => {

    console.log('players/PlayersList');

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

    const listItems = seats.map(seat =>
        <div className='mt-0 sm:mt-[2vh]' key={seat.seat}>
            <PlayerAvatar playerName={seat.name} character={seat.character ?? undefined} />
        </div>
    );

    const classGrid = ' grid grid-cols-' + numberOfPlayers + ' sm:grid-cols-1 gap-[4vw] sm:gap-[1vh] ';
    const classDiv = classGrid + ' flex items-center justify-items-center ';

    return (
        <div className={classDiv}>
            {listItems}
        </div>
    );
}
