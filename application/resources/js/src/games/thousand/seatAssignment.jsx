import {useThousandStore} from "./useThousandStore.jsx";

export const seatAssignment = () => {

    console.log('seatAssignment');
    const orderedPlayers = useThousandStore.getState().situation.orderedPlayers;
    const yourSeat = orderedPlayers[window.MyDramGames.player.name].seat;
    const names = Object.getOwnPropertyNames(orderedPlayers);
    const numberOfPlayers = names.length;

    const seats = new Array(4);
    names.forEach((name) => {
        seats[orderedPlayers[name].seat] = name;
    });

    const leftHandSeat = (yourSeat % numberOfPlayers) + 1;
    const rightHandSeat = ((yourSeat + numberOfPlayers - 2) % numberOfPlayers) + 1;
    const frontSeat = numberOfPlayers === 4 ? (((yourSeat + 1) % 4) + 1) : undefined;

    return [
        seats[leftHandSeat],
        numberOfPlayers === 4 ? seats[frontSeat] : undefined,
        seats[rightHandSeat],
    ];
}
