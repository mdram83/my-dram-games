import React from "react";
import {configThousand} from "../configThousand.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {useThousandStore} from "../useThousandStore.jsx";
import {OpponentInfoBidding} from "./OpponentInfobBidding.jsx";
import {ActionBid} from "./ActionBid.jsx";

export const PlayerInfoBidding = ({playerName, fourPlayersGame}) => {

    console.log(' call PlayerInfoBidding');

    // TODO when setting state in Thousand store, check if bidding phase and save it to dedicated isPhaseBidding store value (will not change with each phase and limit rerenders)
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseBidding = phaseKey === 'bidding';

    const activePlayer = useGamePlayStore(state => state.activePlayer);
    const isActivePlayer = playerName === activePlayer;

    const bidAmount = useThousandStore(state => state.situation.bidAmount);
    const bid = useThousandStore(state => state.situation.orderedPlayers[playerName].bid);

    const seat = useThousandStore(state => state.situation.orderedPlayers[playerName].seat);
    const borderColorClass = configThousand[seat].borderColorClass;
    const singleClassName = ' flex justify-center items-center py-[2vh] ';


    const renderBid = () => {

        if (!isPhaseBidding) {
            return <></>;
        }

        return (
            <div
                className={borderColorClass + ' w-[40%] sm:w-[20%] border-2 border-solid bg-white rounded-xl py-[1vh] px-[4%] bg-opacity-60 '}>

                <div className={singleClassName}>
                    <OpponentInfoBidding playerName={playerName} isFourPlayersDealer={false}/>
                </div>

                <div className={singleClassName}>
                    <ActionBid decision='bid' phase={phaseKey} disabled={!isActivePlayer || bid === 'pass'} amount={bidAmount + 10} />
                </div>

                <div className={singleClassName}>
                    <ActionBid decision='pass' phase={phaseKey} disabled={!isActivePlayer || bid === 'pass'}/>
                </div>

            </div>

        );
    }

    return renderBid();
}
