import {configNetrunners} from "../../configNetrunners.jsx";
import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PowerDetails} from "./PowerDetails.jsx";

export const HardwareDetails = ({player, addClass}) => {

    console.log('HardwareDetails', player);

    const hardwarePower = useNetrunnersStore(state => state.situation.battle.hardwarePower);
    const hardwareItems = useNetrunnersStore(state => state.situation.players[player].hardwareItems);

    const classDiv = ' h-[80%] bg-center bg-no-repeat bg-cover aspect-square rounded-lg mr-[1vh] border-[0.4vh] border-solid '
        + ' border-neutral-500 ';

    const renderHardwareItems = () => hardwareItems.map((item, index) => {
        const styles = item === null
        ? {backgroundImage: configNetrunners.covers.location.imageCoverM, filter: 'grayscale(100%)'}
        : {backgroundImage: configNetrunners.covers.hacked} // add proper picture when you have one and when you test further

        return <div key={index} className={classDiv} style={styles}></div>;
    });

    return (
        <div className={addClass}>
            <div className='flex h-full items-center justify-center'>
                {renderHardwareItems()}
            </div>
            <PowerDetails power={hardwarePower}/>
        </div>
    );
}
