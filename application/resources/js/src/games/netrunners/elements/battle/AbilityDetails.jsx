import {configNetrunners} from "../../configNetrunners.jsx";
import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PowerDetails} from "./PowerDetails.jsx";

export const AbilityDetails = ({player, addClass}) => {

    console.log('AbilityDetails', player);

    const abilityPower = useNetrunnersStore(state => state.situation.battle.abilityPower);
    const ability = useNetrunnersStore(state => state.situation.battle.ability);

    /* Active:
    AttackLosingPower

    Passive:
    WinDraw
    FirstMoveAttackBonus
     */

    const powerAbilities = ['AttackLosingPower', 'WinDraw', 'FirstMoveAttackBonus'];

    const hasPowerAbility = ability !== null && powerAbilities.includes(ability.key);
    const abilityKey = hasPowerAbility ? ability.key : undefined;
    const abilityInUse = hasPowerAbility ? ability.inUse : undefined;

    const classDiv = ' h-[70%] bg-center bg-no-repeat bg-cover aspect-square rounded-lg mr-[2vh] border-[0.4vh] border-solid '
        + ' border-neutral-500 ';

    const classNoPowerAbilities = ' font-sans text-neutral-600 text-[2vh] uppercase ';

    const styles = !hasPowerAbility
        ? {backgroundColor: 'rgb(23 23 23)'}
        : {
            backgroundImage: configNetrunners.covers.abilities[abilityKey],
            filter: (abilityInUse ? 'grayscale(0)' : 'grayscale(100%)'),
        }

    return (
        <div className={addClass}>
            <div className='flex h-full items-center justify-center'>
                {hasPowerAbility && <div className={classDiv} style={styles}></div>}
                {!hasPowerAbility && <div className={classNoPowerAbilities}>No power ability available</div>}
            </div>
            <PowerDetails power={abilityPower}/>
        </div>
    );
}
