import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";
import {calculateRotation} from "../../../../customFunctions/calculateRotation.jsx";
import {Attribute} from "../misc/Attribute.jsx";

export const LocationEncounter = ({row, column, parentRotation = 0}) => {

    console.log('LocationEncounter', row, column);

    const key = useNetrunnersStore(state => state.locationsMap[row][column].encounter.key);
    const isEnemy = useNetrunnersStore(state => state.locationsMap[row][column].encounter.isEnemy);
    const power = useNetrunnersStore(state => state.locationsMap[row][column].encounter.power);

    const classDivCommon =
        ' bg-cover bg-center bg-no-repeat w-[65%] sm:w-[50%] h-[65%] sm:h-[50%]'
        + ' border border-solid border-[0.4vh] rounded-lg '
        + (isEnemy ? configNetrunners.encounters.classEnemyBorder : configNetrunners.encounters.classItemBorder);

    const classDivAttribute = ' absolute top-[0.1vh] right-[0.1vh] border-neutral-300 '
        + (isEnemy ? ' text-pink-600 sm:border-pink-600 ' : ' text-green-800 sm:border-green-800 ');

    const rotation = -parentRotation + calculateRotation(-20, 20, row, column, key, parentRotation);

    const styles = {
        backgroundImage: configNetrunners.encounters[key].imageS,
        position: 'absolute',
        left: '50%',
        top: '50%',
        transform: ` translate(-50%, -50%)  rotate(${rotation}deg) `,
    };



    return (
        <div className={classDivCommon} style={styles}>
            {power > 0 &&
                <Attribute className={classDivAttribute} sizeVh={3} sizeVhSm={2.5}>
                    {power}
                </Attribute>
            }
        </div>
    );
}
