import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {configThousand} from "../configThousand.jsx";
import {ActionDistribute} from "./ActionDistribute.jsx";

export const PlayerInfoStockDistribution = () => {

    console.log('PlayerInfoStockDistribution');

    // TODO when setting state in Thousand store, check if bidding phase and save it to dedicated isPhaseStockDistribution store value (will not change with each phase and limit rerenders)
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseStockDistribution = phaseKey === 'stock-distribution';

    const playerName = window.MyDramGames.player.name;
    const activePlayer = useGamePlayStore(state => state.activePlayer);
    const isActivePlayer = playerName === activePlayer;

    const stockDistribution = useThousandStore(state => state.stockDistribution);
    const countDistribution = Object.values(stockDistribution).filter((cardKey) => !!cardKey).length;

    const seat = useThousandStore(state => state.situation.orderedPlayers[playerName].seat);
    const borderColorClass = configThousand[seat].borderColorClass;
    const singleClassName = ' flex justify-center items-center py-[2vh] ';

    const renderDistribution = () => {

        if (!isPhaseStockDistribution || !isActivePlayer) {
            return <></>;
        }

        return (
            <div
                className={borderColorClass + ' w-full border-2 border-solid bg-white rounded-xl py-[1vh] px-[6%] bg-opacity-60 '}>

                <div className={singleClassName}>
                    <span className="font-sans text-[3vh] font-bold text-orange-600 text-center">
                        DISTRIBUTE CARDS
                    </span>
                </div>

                <div className={singleClassName}>
                    <ActionDistribute phase={phaseKey} disabled={countDistribution !== 2} distribution={stockDistribution} />
                </div>

            </div>

        );
    }

    return renderDistribution();
}
