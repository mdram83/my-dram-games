import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";

export const InventorySlot = ({classAdd, slotKey, item, itemType, pickUp = false}) => {

    console.log('InventorySlot');

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const itemKey = item !== null ? item.key : null;

    const config = {
        Hardware: {
            payloadKey: 'hardware',
            emptyEncounterKey: 'Small',
        },
        Software: {
            payloadKey: 'software',
            emptyEncounterKey: 'Overclock',
        },
    };

    const onClick = () => {
        if (!pickUp) {
            return;
        }

        const payload = {};
        payload[config[itemType].payloadKey] = slotKey;

        submitMove(payload, gamePlayId, setMessage, 'item');
    }

    const styles = {
        backgroundImage: configNetrunners.encounters[itemKey ?? config[itemType].emptyEncounterKey].imageM,
        opacity: !!itemKey ? 1 : 0.25,
        filter: ` grayscale(${!!itemKey ? 0 : 100}%) brightness(${!!itemKey ? 100 : 200}%) `,
    };

    const classDiv = pickUp ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ' : 'border-neutral-500 ';


    return (
        <div className={classAdd + classDiv} onClick={onClick}>
            <div className='size-full bg-center bg-no-repeat bg-cover rounded-lg' style={styles}></div>
        </div>
    );
}
