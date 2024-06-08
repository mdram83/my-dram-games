import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {minicardsSvg} from "../../../../template/elements/playing-cards/decks/minicardsSvg.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {TableDistribution} from "./TableDistribution.jsx";

export const Table = () => {

    console.log('Table');

    // TODO 2. add marriage meld info section, to be available with active card being Q/K prior to sending to table
    // TODO 3. adjsut styling so that first card is rotated by 5deg, second by 105deg, 3rd by 265deg

    const table = useThousandStore(state => state.situation.table);
    const activeCardKey = useThousandStore(state => state.activeCardKey);

    const playerName = window.MyDramGames.player.name;
    const activePlayer = useGamePlayStore(state => state.activePlayer);
    const isActivePlayer = playerName === activePlayer;

    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhasePlayingFirstCard = phaseKey === 'playing-first-card';
    const isPhasePlayingCard = (
        phaseKey === 'playing-first-card'
        || phaseKey === 'playing-second-card'
        || phaseKey === 'playing-third-card'
    );

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
            {isPhasePlayingCard && renderItems()}
            {isPhasePlayingCard && isActivePlayer &&
                <div className="absolute z-10">
                    <TableDistribution/>
                </div>
            }
        </div>
    );
}
