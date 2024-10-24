import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";

export const Location = ({row, column}) => {

    console.log('Location', row, column);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const hasNode = useNetrunnersStore(state => state.locationsMap[row][column].hasNode);
    const nodeKey = useNetrunnersStore(state => state.locationsMap[row][column].nodeKey);
    const nodeRotation = useNetrunnersStore(state => state.locationsMap[row][column].nodeRotation);

    // FIXME something wrong with nodeStock, they are not shuffled... Possibly on load...?
    // TODO then add rotation div (to rotate/direction) and fixed button, hell yeah!
    // TODO then on display (after setting and refresh) utilize nodeRotation to show proper rotation of locations with nodeRotation already given.

    const actionableLocation = useNetrunnersStore(state => state.locationsMap[row][column].actionableLocation);
    const yourActionableLocation = useNetrunnersStore(state => state.locationsMap[row][column].yourActionableLocation);
    const actionablePhaseKey = useNetrunnersStore(state => state.locationsMap[row][column].actionablePhaseKey);

    const style = {
        backgroundImage: hasNode ? configNetrunners.covers.location[nodeKey] : configNetrunners.covers.location.imageCoverM,
    };

    const classDiv = ' bg-cover bg-center bg-no-repeat w-full h-full rounded-md '
        + (actionableLocation
            ? ' border-solid border-[2px] -mt-[2px] -ml-[2px] '
                + (yourActionableLocation
                    ? ' border-orange-500 cursor-pointer shadow-actionSm hover:shadow-actionLg '
                    : ' border-cyan-500 shadow-actionSmOp ')
            : ' ');

    const onClick = () => {
        if (!actionableLocation || !yourActionableLocation) {
            return;
        }
        switch (actionablePhaseKey) {
            case 'location':
                submitMove({row: row, column: column}, gamePlayId , setMessage, actionablePhaseKey);
                return;
            case 'direction':
                // FIXME actually on click I should rotate and submitMove only on Done/Save/Confirm button
                submitMove({row: row, column: column, direction: 0}, gamePlayId , setMessage, actionablePhaseKey);
                return;
            default:
                return;
        }
    }

    const render = () => {
        if (!hasNode && !actionableLocation) {
            return;
        }
        return (
            <div className={classDiv} style={style} onClick={() => onClick()}>{row}.{column}</div>
        );
    }

    return render();
}
