import {configNetrunners} from "../../configNetrunners.jsx";
import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PowerDetails} from "./PowerDetails.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {submitMove} from "../../submitMove.jsx";

export const DiceDetails = ({player, addClass}) => {

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);

    const dices = useNetrunnersStore(state => state.situation.battle.dices);
    const ability = useNetrunnersStore(state => state.situation.battle.ability);

    const diceAbilities = ['ReRollDices', 'ReRollOne'];
    const hasDiceAbility = ability !== null && diceAbilities.includes(ability.key);
    const abilityInUse = hasDiceAbility ? ability.inUse : undefined;
    const canUseDiceAbility = yourTurn && hasDiceAbility && !abilityInUse;

    const styleImage = {
        backgroundImage: configNetrunners.covers.diceS,
    };

    const handleAbility = () => {
        if (canUseDiceAbility) {
            submitMove({ability: true}, gamePlayId , setMessage, 'battle');
        }
    }

    const classDiv = ' h-[70%] bg-center bg-no-repeat bg-cover aspect-square rounded-lg mr-[2vh] border-[0.4vh] border-solid '
        + (canUseDiceAbility ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ' : ' border-neutral-500 ');

    return (
        <div className={addClass}>
            <div className='flex h-full items-center justify-center'>
                <div className={classDiv} style={styleImage} onClick={handleAbility}></div>
            </div>
            <PowerDetails power={`${dices[0]} | ${dices[1]}`} addClass=' text-[3.5vh] -mr-[4vh] ' />
            <PowerDetails power={dices[0] + dices[1]}/>
        </div>
    );
}
