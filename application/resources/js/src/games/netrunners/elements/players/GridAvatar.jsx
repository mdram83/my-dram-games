import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";

export const GridAvatar = ({gridKey}) => {

    const characterName = useNetrunnersStore(state => state.situation.charactersGrid[gridKey].name);
    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const phase = useNetrunnersStore(state => state.situation.phase.key);

    const image = characterName !== null
        ? configNetrunners.characters[characterName].imageAvatarM
        : configNetrunners.covers.character.imageCoverM;

    const style = {
        backgroundImage: image ?? '',
        filter: characterName !== null ? 'grayscale(100%)' : 'grayscale(0)',
    };

    const classDiv = ' h-[16vh] sm:h-[20vh] w-[16vh] sm:w-[20vh] bg-contain rounded-xl '
        + ' border-solid border-[0.5vh] shadow-md '
        + (characterName === null ? ' border-orange-500 shadow-orange-500 cursor-pointer ' : ' ');

    const onClick = () => {
        if (characterName !== null) {
            return;
        }
        submitMove({gridElement: gridKey}, gamePlayId, setMessage, phase);
    }

    return (
        <div className={classDiv}
             style={style}
             onClick={() => onClick()}
        ></div>
    );
}
