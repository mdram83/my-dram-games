import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {CharacterAvatar} from "./CharacterAvatar.jsx";
import {CharacterSelection} from "./CharacterSelection.jsx";

export const CharactersGridItem = ({gridKey}) => {

    console.log('players/CharactersGridItem', gridKey);

    const characterName = useNetrunnersStore(state => state.situation.charactersGrid[gridKey].name);

    const classPartCommon = ' rounded-[4vh] ';

    const renderElement = () => {
        return characterName
            ? <CharacterAvatar characterName={characterName} classPartCommon={classPartCommon} />
            : <CharacterSelection gridKey={gridKey} classPartCommon={classPartCommon} />;
    }

    return (
        <div className={' h-[16vh] sm:h-[20vh] w-[16vh] sm:w-[20vh] ' + classPartCommon}>
            {renderElement()}
        </div>
    );
}
