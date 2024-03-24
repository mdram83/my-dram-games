import React from "react";
import {PlayerStatus} from "../../../template/play/components/PlayerStatus.jsx";

export const StatusBarTicTacToe = ({activePlayer, characters}) => {

    const characterDisplay = {
        x: {
            avatar: '\u2715',
            borderColorClass: 'border-green-600',
        },
        o: {
            avatar: '\u25EF',
            borderColorClass: 'border-pink-600',
        },
    }

    const getAvatarElement = (characterName) => {
        return <span className="text-gray-800 text-lg sm:text-2xl">{characterDisplay[characterName].avatar}</span>;
    }

    const renderCharacters = (characters) => {
        const list = [];
        for (const [characterName, playerName] of Object.entries(characters)) {
            list.push(
                <PlayerStatus isCurrent={activePlayer === playerName}
                              avatarElement={getAvatarElement(characterName)}
                              borderColorClass={characterDisplay[characterName].borderColorClass}
                              name={playerName}
                              key={playerName} />
            );
        }
        return list;
    }


    return (
        <div className="text-gray-200 font-sans font-semibold text-sm sm:text-xl">
            <div className="grid grid-cols-2">

                {renderCharacters(characters)}

            </div>
        </div>
    );
}
