import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const Outcome = ({player}) => {

    console.log('Outcome');

    const character = useNetrunnersStore(state => state.situation.players[player].character);
    const totalPower = useNetrunnersStore(state => state.situation.battle.totalPower);
    const battleOutcome = useNetrunnersStore(state => state.situation.battle.battleOutcome);

    const styleImage = {
        backgroundImage: configNetrunners.characters[character].imageAvatarS,
    };

    const classOutcome = battleOutcome < 0 ? ' text-pink-600 ' : (battleOutcome > 0 ? ' text-green-600 ' : '  text-orange-600 ');
    const labelOutcome = 'draw'; //battleOutcome < 0 ? 'lost' : (battleOutcome > 0 ? 'win' : 'draw');

    const classImage = ' aspect-square bg-top bg-no-repeat bg-cover rounded-lg border border-solid border-[0.4vh] '
        + configNetrunners.characters[character].classAvatarBorder;
    const classPower = ' aspect-square flex items-center justify-center font-sans font-semibold text-[10vh] '
        + classOutcome;
    const classLabel = ' flex items-center justify-center font-sans font-semibold text-[6vh] capitalize -mr-[4vh] '
        + classOutcome;

    return (
        <div className='flex size-full justify-between'>
            <div className={classImage} style={styleImage}></div>
            <div className={classLabel}>{labelOutcome}</div>
            <div className={classPower}><div>{totalPower}</div></div>
        </div>
    );
}
