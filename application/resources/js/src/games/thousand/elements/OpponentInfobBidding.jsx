import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";

export const OpponentInfoBidding = ({playerName, isFourPlayersDealer}) => {

    console.log('  call OpponentInfoBidding for ' + playerName);

    // TODO when setting state in Thousand store, check if bidding phase and save it to dedicated isPhaseBidding store value (will not change with each phase and limit rerenders)
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseBidding = phaseKey === 'bidding';

    const bid = useThousandStore(state => state.situation.orderedPlayers[playerName].bid);

    const renderBid = () => {

        const commonClassName = ' font-sans font-bold ';
        let bidMessage = 'BID: ' + bid;
        let textClassName = ' text-orange-600 ';

        if (bid === null) {
            bidMessage = 'NO BID YET';
        }

        if (bid === 'pass') {
            bidMessage = 'PASS';
            textClassName = ' text-green-900 ';
        }

        if (bid > 120) {
            textClassName = ' text-red-600 ';
        }

        return <span className={commonClassName + textClassName}>{bidMessage}</span>;
    }

    return <div>{!isFourPlayersDealer && isPhaseBidding && renderBid()}</div>;
}