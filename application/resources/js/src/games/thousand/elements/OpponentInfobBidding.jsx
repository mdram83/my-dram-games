import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";

export const OpponentInfoBidding = ({playerName, isFourPlayersDealer}) => {

    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseBidding = phaseKey === 'bidding';

    const bid = useThousandStore(state => state.situation.orderedPlayers[playerName].bid);
    const bidAmount = useThousandStore(state => state.situation.bidAmount);

    const renderBid = () => {

        const commonClassName = ' font-sans text-[3vh] ' + (bid === bidAmount ? ' font-black ' : ' font-bold ');
        let bidMessage = 'BID: ' + bid;
        let textClassName = ' text-orange-600 ';

        if (bid === null) {
            bidMessage = 'BID: ...';
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
