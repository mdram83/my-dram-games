import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";

export const PlayerHand = ({playerName}) => {

    console.log(' call PlayerHand');

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);

    console.log(hand);

    return (
        <div>{hand.length}</div>
    );
}
