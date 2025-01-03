import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";

export const KeySlot = ({classAdd, hasDatabaseKey, pickUp = false}) => {

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const onClick = () => {
        if (!pickUp) {
            return;
        }
        submitMove({key: true}, gamePlayId, setMessage, 'item');
    }

    const styles = {
        backgroundImage: configNetrunners.encounters.key.imageM,
        opacity: hasDatabaseKey ? 1 : 0.25,
        filter: ` grayscale(${hasDatabaseKey ? 0 : 100}%) brightness(${hasDatabaseKey ? 100 : 200}%) `,
    };

    const classDiv = pickUp ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ' : 'border-neutral-500 ';


    return (
        <div className={classAdd + classDiv} onClick={onClick}>
            <div className='size-full bg-center bg-no-repeat bg-cover rounded-lg' style={styles}></div>
        </div>
    );
}
