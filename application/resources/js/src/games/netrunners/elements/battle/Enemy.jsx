import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const Enemy = ({enemyKey, power}) => {

    const battle = useNetrunnersStore(state => state.situation.battle);
    const isBattle = battle !== null;
    const battleOutcome = isBattle ? battle.battleOutcome : undefined;

    const labelOutcome = isBattle ? (battleOutcome < 0 ? 'win' : (battleOutcome > 0 ? 'lost' : 'draw')) : '';

    const styleImage = {
        backgroundImage: configNetrunners.encounters[enemyKey].imageM,
    };

    const classImage = ' aspect-square bg-top bg-no-repeat bg-cover rounded-lg border border-solid border-[0.4vh] border-pink-600 ';
    const classPower = ' aspect-square flex items-center justify-center text-pink-600 font-sans font-semibold text-[10vh] ';
    const classLabel = ' flex items-center justify-center font-sans font-semibold text-[5vh] text-pink-600 uppercase -mr-[4vh] '

    return (
        <div className='flex size-full justify-between'>
            <div className={classImage} style={styleImage}></div>
            <div className={classLabel}>{labelOutcome}</div>
            <div className={classPower}><div>{power}</div></div>
        </div>
    );
}
