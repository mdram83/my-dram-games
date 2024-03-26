import React from "react";
import {PlayerStatus} from "../../../template/play/components/PlayerStatus.jsx";
import {configTicTacToe} from "./configTicTacToe.jsx";
import {useTicTacToeStore} from "./useTicTacToeStore.jsx";

export const StatusBarTicTacToe = ({characters}) => {

    // TODO move to PlayerStatus so that I don't rerender whole grid with every update
    const activePlayer = useTicTacToeStore((state) => state.activePlayer);

    const getAvatarElement = (characterName) => {
        return <span className="text-gray-800 text-[2.8vh] sm:text-[3.2vh]">{configTicTacToe[characterName].avatar}</span>;
    }

    const renderCharacters = (characters) => {
        const list = [];
        for (const [characterName, playerName] of Object.entries(characters)) {
            list.push(
                <PlayerStatus isCurrent={activePlayer === playerName}
                              avatarElement={getAvatarElement(characterName)}
                              borderColorClass={configTicTacToe[characterName].borderColorClass}
                              name={playerName}
                              key={playerName} />
            );
        }
        return list;
    }

    return (
        <div className="w-[96%] h-full text-gray-200 font-sans font-semibold text-[2.2vh] sm:text-[2.6vh]">
            <div className="grid grid-cols-2 h-full">

                {renderCharacters(characters)}

            </div>
        </div>
    );
}
