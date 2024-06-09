import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";

export const OpponentInfoReady = ({playerName}) => {

    console.log('OpponentInfoReady:' + playerName);

    // TODO when setting state in Thousand store, check if bidding phase and save it to dedicated isPhaseBidding store value (will not change with each phase and limit rerenders)
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseCountingPoints = phaseKey === 'counting-points';

    const ready = useThousandStore(state => state.situation.orderedPlayers[playerName].ready);

    const renderReady = () => {

        const commonClassName = ' font-sans text-[3vh] font-bold ';
        const readyMessage = ready ? 'READY' : 'NOT READY';
        const textClassName = ready ? ' text-green-900 ' : ' text-orange-600 ';

        return <span className={commonClassName + textClassName}>{readyMessage}</span>;
    }

    return <div>{isPhaseCountingPoints && renderReady()}</div>;
}
