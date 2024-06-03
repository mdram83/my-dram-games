import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {minicardsSvg} from "../../../../template/elements/playing-cards/decks/minicardsSvg.jsx";

export const OpponentStockDistribution = ({playerName, fourPlayersGame}) => {

    console.log('OpponentStockDistribution:', playerName);

    // TODO when setting state in Thousand store, check if bidding phase and save it to dedicated isPhaseBidding store value (will not change with each phase and limit rerenders)
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const dealer = useThousandStore(state => state.situation.dealer);
    const activePlayer = useThousandStore(state => state.situation.activePlayer);
    const stockDistribution = useThousandStore(state => state.stockDistribution[playerName]);
    const setStockDistribution = useThousandStore(state => state.setStockDistribution)
    const activeCardKey = useThousandStore(state => state.activeCardKey);
    const setActiveCardKey = useThousandStore(state => state.setActiveCardKey);

    const isPhaseStockDistribution = phaseKey === 'stock-distribution';

    const isActive = window.MyDramGames.player.name === activePlayer;
    const isDealer = playerName === dealer;
    const isFourPlayersDealer = isDealer && fourPlayersGame;

    const isActiveCard = activeCardKey !== null;
    const hasStockDistribution = !!stockDistribution;

    const isPlayable = isActiveCard && !hasStockDistribution;

    const divClassBase = ' w-[60px] h-[84px] '
        + ' flex justify-center items-center '
        + ' bg-white bg-opacity-30 shadow-md'
        + ' border-solid border-[0.5vh] rounded-lg ';
    const divClassBorder = isPlayable ? ' border-orange-500 shadow-orange-500 cursor-pointer ' : ' border-gray-500 ';
    const divClassCard = hasStockDistribution ? ' cursor-pointer ' : ' ';

    const divStyleCard = {
        transform: 'scale(0.88)',
    }


    const toggleCard = () => {
        if (!isPlayable && !hasStockDistribution) {
            console.log('No actions');
            return null;
        }
        if (isPlayable) {
            console.log('Distribute card:', activeCardKey);
            setStockDistribution(playerName, activeCardKey);
            setActiveCardKey(null);
            return null;
        }
        if (hasStockDistribution) {
            console.log('Return card:', stockDistribution);
            setStockDistribution(playerName, null);
            return null;
        }
    }

    const getCard = (cardKey) => {
        return minicardsSvg[cardKey];
    }

    return (
        <>
            {
                isActive && isPhaseStockDistribution && !isFourPlayersDealer &&
                <div className="flex items-center justify-center mt-[3vh] sm:mt-[10vh] w-full z-20">
                    <div className={divClassBase + divClassBorder + divClassCard} onClick={toggleCard}>
                        <div style={divStyleCard}>
                            {hasStockDistribution && getCard(stockDistribution)}
                        </div>
                    </div>
                </div>
            }
        </>
    );
}
