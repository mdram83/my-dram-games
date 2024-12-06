import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {DiceDetails} from "./DiceDetails.jsx";
import {HardwareDetails} from "./HardwareDetails.jsx";

export const BattleDetails = ({player}) => {

    console.log('BattleDetails', player);


    const softwarePower = useNetrunnersStore(state => state.situation.battle.softwarePower);
    const abilityPower = useNetrunnersStore(state => state.situation.battle.abilityPower);

    const classBattleLine = ' flex h-full w-[95%] justify-between items-center ';
    const classBattleItem = ' flex h-full aspect-square items-center justify-center ';
    const classScore = ' font-sans font-semibold text-[5vh] text-green-600 ';

    const BattleLine = ({children}) => <div className={classBattleLine}>{children}</div>;
    const BattleItem = ({children, addClass = ''}) => <div className={classBattleItem + addClass}>{children}</div>

    return (
        <div className='grid grid-rows-4 h-full'>

            <DiceDetails player={player} addClass={classBattleLine} />
            <HardwareDetails player={player} addClass={classBattleLine} />

            <BattleLine>
                <BattleItem>Software</BattleItem>
                <BattleItem addClass={classScore}>{softwarePower}</BattleItem>
            </BattleLine>

            <BattleLine>
                <BattleItem>Abilities</BattleItem>
                <BattleItem addClass={classScore}>{abilityPower}</BattleItem>
            </BattleLine>

        </div>
    );
}
