import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {configThousand} from "../configThousand.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";

export const Avatar = ({playerName}) => {

    console.log(' call Avatar for ' + playerName);

    const isConnected = useGamePlayStore((state) => state.players[playerName]);
    const activePlayer = useGamePlayStore((state) => state.activePlayer);
    const isCurrent = activePlayer === playerName;

    const initial = playerName.charAt(0).toUpperCase();
    const seat = useThousandStore(state => state.situation.orderedPlayers[playerName].seat);
    const borderColorClass = configThousand[seat].borderColorClass;
    const avatarTextClass = configThousand[seat].avatarTextClass;

    const circleClass =
        ' flex items-center justify-center w-[8vh] sm:w-[10vh] h-[8vh] sm:h-[10vh] '
        + borderColorClass + ' rounded-full '
        + (!isConnected ? ' animate-pulse bg-gray-200 ' : ' ')
        + (isCurrent ? ' border-[1vh] border-solid bg-white ' : ' border-[0.4vh] border-solid bg-gray-200 ');

    const initialClass = ' text-[4vh] sm:text-[5vh] font-sans pb-[0.5vh] '
        + (isCurrent ? (' font-black ' + avatarTextClass) : (' font-bold text-gray-600 '));

    return (
        <div className={circleClass}>
            <span className={initialClass}>{initial}</span>
        </div>
    );
}
