import React, {useEffect} from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {configThousand} from "../configThousand.jsx";

export const PlayerInfoMarriageMelding = () => {

    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhasePlayingFirstCard = phaseKey === 'playing-first-card';

    const playerName = window.MyDramGames.player.name;
    const activePlayer = useGamePlayStore(state => state.activePlayer);
    const isActivePlayer = playerName === activePlayer;

    const seat = useThousandStore(state => state.situation.orderedPlayers[playerName].seat);
    const borderColorClass = configThousand[seat].borderColorClass;
    const singleClassName = ' flex justify-center items-center py-[2vh] ';

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);
    const meldMarriageDecision = useThousandStore(state => state.meldMarriageDecision);
    const setMeldMarriageDecision = useThousandStore(state => state.setMeldMarriageDecision);
    const activeCardKey = useThousandStore(state => state.activeCardKey);
    const hasActiveCard = activeCardKey !== null;

    const isCardMarriagePart = () => {

        if (!hasActiveCard) {
            return false;
        }

        const activeCardRank = activeCardKey.split('')[0];
        const activeCardSuit = activeCardKey.split('')[2];

        if (activeCardRank !== 'K' && activeCardRank !== 'Q') {
            return false;
        }

        const expectedMarriagePairRank = activeCardRank === 'K' ? 'Q' : 'K';
        const expectedMarriagePairKey = expectedMarriagePairRank + '-' + activeCardSuit;

        return hand.includes(expectedMarriagePairKey);
    }

    const toggleMeldMarriageDecision = () => {
        setMeldMarriageDecision(!meldMarriageDecision);
    }

    useEffect(() => {
        setMeldMarriageDecision(false);
    }, [activeCardKey]);


    const renderMelding = () => {

        if (!isPhasePlayingFirstCard || !isActivePlayer || !hasActiveCard || !isCardMarriagePart()) {
            return <></>;
        }

        return (
            <div className={borderColorClass + ' w-full border-[0.4vh] border-solid bg-white rounded-xl bg-opacity-60 px-[2vh] '}>

                <div className={singleClassName}>
                    <div className="w-fit sm:w-[4vh] h-fit sm:h-[4vh] mr-[0.4vh] sm:mr-[2vh] border-[0.5vh] border-solid border-orange-500 shadow-md shadow-orange-500 rounded-lg flex items-center justify-center justify-content-center">
                        <input type="checkbox" className="accent-orange-500" onClick={toggleMeldMarriageDecision} style={{transform: 'scale(1.5)'}}/>
                    </div>
                    <div className="w-auto font-sans text-[2.6vh] font-bold text-orange-600 text-center">
                        Check to Meld Marriage
                    </div>
                </div>

            </div>

        );
    }

    return renderMelding();
}
