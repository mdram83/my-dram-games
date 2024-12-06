import {configNetrunners} from "../../configNetrunners.jsx";
import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PowerDetails} from "./PowerDetails.jsx";

export const AbilityDetails = ({player, addClass}) => {

    console.log('AbilityDetails', player);

    const abilityPower = useNetrunnersStore(state => state.situation.battle.abilityPower);
    const ability = useNetrunnersStore(state => state.situation.battle.ability);

    const hasAbility = ability !== null;
    const abilityKey = hasAbility ? ability.key : undefined;
    const abilityInUse = hasAbility ? ability.inUse : undefined;

    const classDiv = ' h-[70%] bg-center bg-no-repeat bg-cover aspect-square rounded-lg mr-[2vh] border-[0.4vh] border-solid '
        + ' border-neutral-500 ';

    const styles = !hasAbility
        ? {backgroundColor: 'rgb(23 23 23)'}
        : {
            backgroundImage: configNetrunners.covers.hacked, // add proper picture when you have one and when you test further
            filter: (abilityInUse ? 'grayscale(0)' : 'grayscale(100%)'),
        }


    /* Active:
    AttackLosingPower

    Passive:
    WinDraw
    FirstMoveAttackBonus

    Dices:
    ReRollDices
    ReRollOne
     */

    return (
        <div className={addClass}>
            <div className='flex h-full items-center justify-center'>
                <div className={classDiv} style={styles}></div>
            </div>
            <PowerDetails power={abilityPower}/>
        </div>
    );
}
