import React from "react";
import {configThousand} from "../configThousand.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {useThousandStore} from "../useThousandStore.jsx";
import {OpponentInfoBidding} from "./OpponentInfobBidding.jsx";
import {ActionBid} from "./ActionBid.jsx";

export const PlayerInfoBidding = ({playerName}) => {

    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseBidding = phaseKey === 'bidding';

    const isPlayerFourPlayersDealer = useThousandStore(state => state.isPlayerFourPlayersDealer);

    const activePlayer = useGamePlayStore(state => state.activePlayer);
    const isActivePlayer = playerName === activePlayer;

    const bidAmount = useThousandStore(state => state.situation.bidAmount);
    const bid = useThousandStore(state => state.situation.orderedPlayers[playerName].bid);

    const seat = useThousandStore(state => state.situation.orderedPlayers[playerName].seat);
    const borderColorClass = configThousand[seat].borderColorClass;
    const singleClassName = ' flex justify-center items-center py-[2vh] ';


    const renderBid = () => {

        if (!isPhaseBidding || isPlayerFourPlayersDealer) {
            return <></>;
        }

        return (
            <div
                className={borderColorClass + ' w-full border-[0.4vh] border-solid bg-white rounded-xl bg-opacity-60 px-[2vh] '}>

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
