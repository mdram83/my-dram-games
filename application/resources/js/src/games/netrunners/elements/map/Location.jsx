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

    const hasNode = useNetrunnersStore(state => state.locationsMap[row][column].hasNode);
    const allowedTargetLocation = useNetrunnersStore(state => state.locationsMap[row][column].allowedTargetLocation);
    const yourTargetLocation = useNetrunnersStore(state => state.locationsMap[row][column].yourTargetLocation);

    const style = {
        backgroundImage: hasNode ? configNetrunners.covers.character.imageCoverS : configNetrunners.covers.location.imageCoverM,
    };

    const classDiv = ' bg-contain w-full h-full rounded-md '
        + (allowedTargetLocation
            ? ' border-solid border-[2px] -mt-[2px] -ml-[2px] '
                + (yourTargetLocation
                    ? ' border-orange-500 cursor-pointer shadow-actionSm hover:shadow-actionLg '
                    : ' border-cyan-500 shadow-actionSmOp ')
            : ' ');

    const onClick = () => {
        if (!allowedTargetLocation || !yourTargetLocation) {
            return;
        }
        submitMove({row: row, column: column}, gamePlayId , setMessage, phase);
    }

    const render = () => {
        if (!hasNode && !allowedTargetLocation) {
            return;
        }
        return (
            <div className={classDiv} style={style} onClick={() => onClick()}>{row}.{column}</div>
        );
    }

    return render();
}