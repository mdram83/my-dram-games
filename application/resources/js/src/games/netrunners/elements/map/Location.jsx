import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";

export const Location = ({row, column}) => {

    console.log('Location', row, column);

    const phase = useNetrunnersStore(state => state.situation.phase.key);
    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const location = useNetrunnersStore(state => state.situation.map[row][column]);
    // console.log(location);

    const hasNode = location !== null && location.hasOwnProperty('node');
    const isAllowedTargetLocation = location !== null && location.hasOwnProperty('allowedTargetLocation');

    const style = {
        backgroundImage: hasNode ? configNetrunners.covers.character.imageCoverS : configNetrunners.covers.location.imageCoverM,
    };

    const classDiv = ' bg-contain w-full h-full rounded-md '
        + (isAllowedTargetLocation ? ' border-solid border-[2px] -mt-[2px] -ml-[2px] border-orange-500 cursor-pointer shadow-actionSm hover:shadow-actionLg ' : ' ');

    const onClick = () => {
        if (!isAllowedTargetLocation) {
            return;
        }
        submitMove({row: row, column: column}, gamePlayId, setMessage, phase);
    }

    const render = () => {
        if (!hasNode && !isAllowedTargetLocation) {
            return;
        }
        return (
            <div className={classDiv} style={style} onClick={() => onClick()}>{row}.{column}</div>
        );
    }

    return render();
}
