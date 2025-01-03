import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {CharacterAvatar} from "./CharacterAvatar.jsx";
import {CharacterSelection} from "./CharacterSelection.jsx";

export const CharactersGridItem = ({gridKey}) => {

    const characterName = useNetrunnersStore(state => state.situation.charactersGrid[gridKey].name);

    const classPartCommon = ' rounded-[4vh] ';
    const classPartSize = ' h-[16vh] sm:h-[20vh] w-[16vh] sm:w-[20vh] ';

    const renderElement = () => {
        return characterName
            ? <CharacterAvatar characterName={characterName} classPartCommon={classPartCommon} classPartSize={classPartSize}/>
            : <CharacterSelection gridKey={gridKey} classPartCommon={classPartCommon} classPartSize={classPartSize} />;
    }

    return (
        <div className={classPartSize + classPartCommon}>
            {renderElement()}
        </div>
    );
}
