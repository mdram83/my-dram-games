import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";

export const GridAvatar = ({gridKey}) => {

    console.log('players/GridAvatar', gridKey);

    const characterName = useNetrunnersStore(state => state.situation.charactersGrid[gridKey].name);
    const phase = useNetrunnersStore(state => state.situation.phase.key);
    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const activePlayer = useGamePlayStore(state => state.activePlayer);

    const isActivePlayer = activePlayer === MyDramGames.player.name;

    const image = characterName !== null
        ? configNetrunners.characters[characterName].imageAvatarM
        : configNetrunners.covers.character.imageCoverM;

    const style = {
        backgroundImage: image,
        filter: characterName !== null ? 'grayscale(0)' : 'grayscale(100%)',
    };

    const classPartDivCommon = ' rounded-[4vh] ';

    const classDivAction = ' h-[16vh] sm:h-[20vh] w-[16vh] sm:w-[20vh] '
        + classPartDivCommon
        + ((characterName === null && isActivePlayer)
                ? ' border-solid border-[0.5vh] border-orange-500 cursor-pointer shadow-actionSm hover:shadow-actionLg '
                : ' '
        );

    const classDivAvatar = ' bg-contain w-full h-full '
        + classPartDivCommon;

    const onClick = () => {
        if (characterName !== null || !isActivePlayer) {
            return;
        }
        submitMove({gridElement: gridKey}, gamePlayId, setMessage, phase);
    }

    return (
        <div className={classDivAction}>
            <div className={classDivAvatar}
                 style={style}
                 onClick={() => onClick()}
            ></div>
        </div>
    );
}
