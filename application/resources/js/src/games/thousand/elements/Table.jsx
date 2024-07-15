import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {minicardsSvg} from "../../../../template/elements/playing-cards/decks/minicardsSvg.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {TableDistribution} from "./TableDistribution.jsx";

export const Table = () => {

    const table = useThousandStore(state => state.situation.table);

    const playerName = window.MyDramGames.player.name;
    const activePlayer = useGamePlayStore(state => state.activePlayer);
    const isActivePlayer = playerName === activePlayer;

    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhasePlayingCard = (
        phaseKey === 'playing-first-card'
        || phaseKey === 'playing-second-card'
        || phaseKey === 'playing-third-card'
    );
    const isTableVisible = isPhasePlayingCard || phaseKey === 'collecting-tricks';

    const getCard = (cardKey) => {
        return minicardsSvg[cardKey];
    }

    const renderItems = () => table.map((cardKey, index) => {
        const cardStyle = {
            transform: `scale(0.88) rotate(${(index * 45 - 10)}deg)`,
            transformOrigin: '70% 70%',
        }
        return <div className="absolute w-[57px] h-[88px] -ml-[29px]" key={cardKey} style={cardStyle}>{getCard(cardKey)}</div>
    });

    return (
        <div className="relative">
            {isTableVisible && renderItems()}
            {isPhasePlayingCard && isActivePlayer &&
                <div className="absolute z-10">
                    <TableDistribution/>
                </div>
            }
        </div>
    );
}
