import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";
import {calculateRotation} from "../../../../customFunctions/calculateRotation.jsx";
import {Attribute} from "../misc/Attribute.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";

export const LocationEncounter = ({row, column, activeItem = false, yourActiveItem = false, parentRotation = 0}) => {

    console.log('LocationEncounter', row, column);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const setPlayerInfoScreen = useNetrunnersStore(state => state.setPlayerInfoScreen);
    const setItemPickUpType = useNetrunnersStore(state => state.setItemPickUpType);

    const key = useNetrunnersStore(state => state.locationsMap[row][column].encounter.key);
    const isEnemy = useNetrunnersStore(state => state.locationsMap[row][column].encounter.isEnemy);
    const power = useNetrunnersStore(state => state.locationsMap[row][column].encounter.power);
    const itemType = useNetrunnersStore(state => state.locationsMap[row][column].encounter.itemType);

    const onClick = () => {
        if (!yourActiveItem) {
            return;
        }

        if (itemType === 'Data') {
            submitMove({data: true}, gamePlayId, setMessage, 'item');
            return;
        }

        setItemPickUpType(itemType);
        setPlayerInfoScreen(true, MyDramGames.player.name);
    }

    const classDivCommon =
        ' bg-cover bg-center bg-no-repeat w-[65%] sm:w-[50%] h-[65%] sm:h-[50%] z-10 '
        + ' border border-solid border-[0.4vh] rounded-lg '
        + (isEnemy ? configNetrunners.encounters.classEnemyBorder : (!activeItem ? configNetrunners.encounters.classItemBorder : (
            yourActiveItem ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ' : ' border-cyan-500 shadow-actionSmOp '
        )));

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
        <div className={classDivCommon} style={styles} onClick={onClick}>
            {power > 0 &&
                <Attribute className={classDivAttribute} sizeVh={3} sizeVhSm={2.5}>
                    {power}
                </Attribute>
            }
        </div>
    );
}
