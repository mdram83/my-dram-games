import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";

export const OpponentHand = ({playerName}) => {

    console.log('OpponentHand:' + playerName);

    const handCount = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);

    return (
        <div>Hand Count: {handCount}</div>
    );

}
