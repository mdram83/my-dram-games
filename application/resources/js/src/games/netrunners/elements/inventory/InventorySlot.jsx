import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";
import {Attribute} from "../misc/Attribute.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const InventorySlot = ({classAdd, slotKey, item, itemType, pickUp = false, playerName = undefined}) => {

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const activePlayer = useGamePlayStore(state => state.activePlayer);

    const phaseKey = useNetrunnersStore(state => state.situation.phase.key);
    const useChargerSoftware = useNetrunnersStore(state => state.useChargerSoftware);
    const setUseChargerSoftware = useNetrunnersStore(state => state.setUseChargerSoftware);
    const setPlayerInfoScreen = useNetrunnersStore(state => state.setPlayerInfoScreen);

    const itemKey = item !== null ? item.key : null;
    const power = item !== null ? item.power : 0;

    const canUseChargerSoftware =
        (phaseKey === 'location' || phaseKey === 'finish')
        && itemKey === 'Charger'
        && pickUp === false
        && playerName === activePlayer
        && playerName === window.MyDramGames.player.name
    ;

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

        if (canUseChargerSoftware) {
            setUseChargerSoftware(useChargerSoftware === undefined ? slotKey : undefined);
            setPlayerInfoScreen(false);
            return;
        }

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

    const classDiv = (pickUp || canUseChargerSoftware) ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ' : 'border-neutral-500 ';


    return (
        <div className={classAdd + classDiv} onClick={onClick}>
            <div className='size-full bg-center bg-no-repeat bg-cover rounded-lg' style={styles}>
                {power > 0 &&
                    <Attribute className=' absolute top-[0.1vh] right-[0.1vh] border-neutral-300 text-green-800 sm:border-green-800 '
                               sizeVh={4}
                               sizeVhSm={3.5}
                    >{power}</Attribute>
                }
            </div>
        </div>
    );
}
