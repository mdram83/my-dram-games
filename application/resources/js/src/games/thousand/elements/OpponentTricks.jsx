import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";

export const OpponentTricks = ({playerName}) => {

    console.log(' call OpponentTricks for ' + playerName);

    const tricksCount = useThousandStore(state => state.situation.orderedPlayers[playerName].tricks);

    return (
        <div>Tricks Count: {tricksCount}</div>
    );

}