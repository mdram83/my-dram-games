import React, {useEffect} from "react";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {useThousandStore} from "../useThousandStore.jsx";

export const GameMessageHandler = () => {

    const setMessage = useGamePlayStore((state) => state.setMessage);
    const messageTimeout = 5;

    const playerName = MyDramGames.player.name;

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);
    const activePlayer = useGamePlayStore((state) => state.activePlayer);
    const isCurrent = activePlayer === playerName;
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const bidAmount = useThousandStore(state => state.situation.bidAmount);

    // Declaration message
    useEffect(() => {
        if (!isCurrent && phaseKey === 'playing-first-card' && hand.length === 8) {
            setMessage(`${activePlayer} declares ${bidAmount} points for this round.`, false, messageTimeout);
        }
    }, [hand, phaseKey]);

    return <></>;
}
