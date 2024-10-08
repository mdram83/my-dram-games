import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const CharacterSelection = ({gridKey, classPartCommon, classPartSize}) => {

    const phase = useNetrunnersStore(state => state.situation.phase.key);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const activePlayer = useGamePlayStore(state => state.activePlayer);

    const isActivePlayer = activePlayer === MyDramGames.player.name;
    const isSelectionAvailable = (isActivePlayer && phase === 'character');

    const classDivAction = classPartSize + classPartCommon + (
        isSelectionAvailable
            ? ' border-solid border-[0.5vh] -mt-[0.5vh] -ml-[0.5vh] border-orange-500 cursor-pointer shadow-actionSm hover:shadow-actionLg '
            : ' '
    );

    const style = {
        backgroundImage: configNetrunners.covers.character.imageCoverM,
        filter: 'grayscale(100%)',
    };

    const onClick = () => {
        if (!isSelectionAvailable) {
            return;
        }
        submitMove({gridElement: gridKey}, gamePlayId, setMessage, phase);
    }

    return (
        <div className={classDivAction}>

            <div className={'bg-contain w-full h-full' + classPartCommon}
                 style={style}
                 onClick={() => onClick()}
            ></div>

        </div>
    );
}
