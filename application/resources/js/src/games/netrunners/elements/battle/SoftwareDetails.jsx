import {configNetrunners} from "../../configNetrunners.jsx";
import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PowerDetails} from "./PowerDetails.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";

export const SoftwareDetails = ({player, addClass}) => {

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);

    const softwarePower = useNetrunnersStore(state => state.situation.battle.softwarePower);
    const softwareInUse = useNetrunnersStore(state => state.situation.battle.softwareInUse);
    const softwareItems = useNetrunnersStore(state => state.situation.players[player].softwareItems);

    const toggleSoftware = (index, inUse) => {
        if (yourTurn && softwareItems[index] !== null && softwareItems[index].power > 0) {
            const software = {};
            software[index] = inUse;
            submitMove({software: software}, gamePlayId , setMessage, 'battle');
        }
    }

    const classDiv = ' h-[70%] bg-center bg-no-repeat bg-cover aspect-square rounded-lg mr-[2vh] border-[0.4vh] border-solid ';

    const renderItems = () => softwareItems.map((item, index) => {

        const styles = item === null
            ? {backgroundColor: 'rgb(23 23 23)'}
            : {
                backgroundImage: configNetrunners.covers.inventory[item.key].imageS,
                filter: softwareInUse[index] ? 'grayscale(0)' : 'grayscale(100%)',
            };

        const classDivBorder = item === null || item.power === 0 || !yourTurn
            ? ' border-neutral-500 '
            : ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ';

        return (
            <div key={index} className={classDiv + classDivBorder} onClick={() => toggleSoftware(index, !softwareInUse[index])}>
                <div className='h-full bg-center bg-no-repeat bg-cover aspect-square rounded-lg' style={styles}></div>
            </div>
        );
    });

    return (
        <div className={addClass}>
            <div className='flex h-full items-center justify-center'>
                {renderItems()}
            </div>
            <PowerDetails power={softwarePower}/>
        </div>
    );
}
