import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";
import {calculateRotation} from "../../../../customFunctions/calculateRotation.jsx";

export const LocationEncounter = ({row, column, parentRotation = 0}) => {

    console.log('LocationEncounter', row, column);

    const key = useNetrunnersStore(state => state.locationsMap[row][column].encounter.key);
    const isEnemy = useNetrunnersStore(state => state.locationsMap[row][column].encounter.isEnemy);

    const classDivCommon =
        ' bg-cover bg-center bg-no-repeat w-[50%] h-[50%] '
        + ' border border-solid border-[0.4vh] rounded-lg '
        + (isEnemy ? configNetrunners.encounters.classEnemyBorder : configNetrunners.encounters.classItemBorder);

    const rotation = -parentRotation + calculateRotation(-10, 10, row, column, key, parentRotation);

    const styles = {
        backgroundImage: configNetrunners.encounters[key].imageS,
        position: 'absolute',
        left: '50%',
        top: '50%',
        transform: ` translate(-50%, -50%)  rotate(${rotation}deg) `,
    };

    return <div className={classDivCommon} style={styles}></div>;
}
