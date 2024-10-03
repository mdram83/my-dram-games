import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";

export const Avatar = ({playerName, character = undefined}) => {

    const isConnected = useGamePlayStore((state) => state.players[playerName]);
    const activePlayer = useGamePlayStore((state) => state.activePlayer);
    const isCurrent = activePlayer === playerName;

    const initials = playerName.charAt(0).toUpperCase();

    const borderColorClass = character ? configNetrunners.characters[character].classAvatarBorder : ' border-orange-600 ';
    const avatarTextClass = character ? configNetrunners.characters[character].classAvatarText : ' text-orange-600 ';
    const imageAvatar = character ? configNetrunners.characters[character].imageAvatarS : undefined;

    const style = {
        backgroundImage: imageAvatar ?? '',
        filter: isCurrent ? 'grayscale(0)' : 'grayscale(100%)',
    };

    const circleClass =
        ' flex items-center justify-center w-[8vh] sm:w-[10vh] h-[8vh] sm:h-[10vh] rounded-full border-[0.6vh] border-solid '
        + borderColorClass
        + (isConnected ? ' ' : ' animate-pulse bg-gray-200 ')
        + (isCurrent ? ' bg-white ' : ' bg-gray-200 ');

    const initialsClass = ' text-[4vh] sm:text-[5vh] font-sans pb-[0.5vh] ' + avatarTextClass
        + (isCurrent ? ' font-black ' : ' font-bold ');

    return (
        <div className={circleClass + ' bg-bottom bg-no-repeat bg-cover '} style={style}>
            <span className={initialsClass}>{character ? '' : initials}</span>
        </div>
    );
}
