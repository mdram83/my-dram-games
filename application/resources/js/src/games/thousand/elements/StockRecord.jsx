import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {minicardsSvg} from "../../../../template/elements/playing-cards/decks/minicardsSvg.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";

export const StockRecord = () => {

    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseStockDistribution = phaseKey === 'stock-distribution';

    const playerName = window.MyDramGames.player.name;
    const activePlayer = useGamePlayStore(state => state.activePlayer);
    const isActivePlayer = playerName === activePlayer;

    const stockRecord = useThousandStore(state => state.situation.stockRecord);

    const getCard = (cardKey) => {
        return minicardsSvg[cardKey];
    }

    const divStyleCard = {
        transform: 'scale(0.88)',
    }

    const renderItems = () => stockRecord.map(
        (cardKey) => <div key={cardKey} style={divStyleCard}>{getCard(cardKey)}</div>
    );

    return (
        <div className="relative">
            {isPhaseStockDistribution && !isActivePlayer &&
                <div className="inline-grid grid-cols-3 gap-0 items-center justify-center w-[100%] ml-0">
                    {renderItems()}
                </div>
            }
        </div>
    );
}
