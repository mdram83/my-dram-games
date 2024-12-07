import {configNetrunners} from "../../configNetrunners.jsx";
import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PowerDetails} from "./PowerDetails.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";

export const AbilityDetails = ({player, addClass}) => {

    console.log('AbilityDetails', player);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);

    const abilityPower = useNetrunnersStore(state => state.situation.battle.abilityPower);
    const ability = useNetrunnersStore(state => state.situation.battle.ability);

    const powerAbilities = ['AttackLosingPower', 'WinDraw', 'FirstMoveAttackBonus'];
    const hasPowerAbility = ability !== null && powerAbilities.includes(ability.key);
    const abilityKey = hasPowerAbility ? ability.key : undefined;
    const abilityInUse = hasPowerAbility ? ability.inUse : undefined;
    const canTogglePowerAbility = yourTurn && hasPowerAbility && abilityKey === 'AttackLosingPower';

    const handleAbility = () => {
        if (canTogglePowerAbility) {
            submitMove({ability: !abilityInUse}, gamePlayId , setMessage, 'battle');
        }
    }

    const classDiv = ' h-[70%] bg-center bg-no-repeat bg-cover aspect-square rounded-lg mr-[2vh] border-[0.4vh] border-solid '
        + (canTogglePowerAbility ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ' : ' border-neutral-500 ');

    const classNoPowerAbilities = ' font-sans text-neutral-600 text-[2vh] uppercase ';

    const styles = !hasPowerAbility
        ? {}
        : {
            backgroundImage: configNetrunners.covers.abilities[abilityKey].imageS,
            filter: (abilityInUse ? 'grayscale(0)' : 'grayscale(100%)'),
        };

    return (
        <div className={addClass}>

            <div className='flex h-full items-center justify-center'>

                {hasPowerAbility && <div className={classDiv} onClick={handleAbility}>
                    <div className='h-full bg-center bg-no-repeat bg-cover aspect-square rounded-lg' style={styles}></div>
                </div>}

                {!hasPowerAbility && <div className={classNoPowerAbilities}>No power ability available</div>}

            </div>

            <PowerDetails power={abilityPower}/>

        </div>
    );
}
