import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {configThousand} from "../configThousand.jsx";
import {ActionCollect} from "./ActionCollect.jsx";

export const PlayerInfoCollect = () => {

    console.log('PlayerInfoCollect');

    // TODO when setting state in Thousand store, check if bidding phase and save it to dedicated isPhaseStockDistribution store value (will not change with each phase and limit rerenders)
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseCollectTricks = phaseKey === 'collecting-tricks';

    const activePlayer = useThousandStore(state => state.situation.activePlayer);
    const playerName = window.MyDramGames.player.name;
    const isPlayerTrickWinner = activePlayer === playerName;

    const seat = useThousandStore(state => state.situation.orderedPlayers[playerName].seat);
    const borderColorClass = configThousand[seat].borderColorClass;
    const singleClassName = ' flex justify-center items-center py-[2vh] ';

    const renderCollect = () => {

        if (!isPhaseCollectTricks || !isPlayerTrickWinner) {
            return <></>;
        }

        return (
            <div
                className={borderColorClass + ' w-full border-[0.4vh] border-solid bg-white rounded-xl bg-opacity-60 px-[2vh] '}>

                <div className={singleClassName}>
                    <span className="font-sans text-[3vh] font-bold text-orange-600 text-center">
                        TRICK IS YOURS
                    </span>
                </div>

                <div className={singleClassName}>
                    <ActionCollect phase={phaseKey} />
                </div>

            </div>

        );
    }

    return renderCollect();
}
