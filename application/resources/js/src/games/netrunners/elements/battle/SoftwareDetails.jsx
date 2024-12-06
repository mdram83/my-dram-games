import {configNetrunners} from "../../configNetrunners.jsx";
import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PowerDetails} from "./PowerDetails.jsx";

export const SoftwareDetails = ({player, addClass}) => {

    console.log('SoftwareDetails', player);

    const softwarePower = useNetrunnersStore(state => state.situation.battle.softwarePower);
    const softwareItems = useNetrunnersStore(state => state.situation.players[player].softwareItems);

    const classDiv = ' h-[70%] bg-center bg-no-repeat bg-cover aspect-square rounded-lg mr-[2vh] border-[0.4vh] border-solid '
        + ' border-neutral-500 ';

    const renderItems = () => softwareItems.map((item, index) => {
        const styles = item === null
        ? {backgroundColor: 'rgb(23 23 23)'}
        : {backgroundImage: configNetrunners.covers.hacked} // add proper picture when you have one and when you test further

        return <div key={index} className={classDiv} style={styles}></div>;
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
