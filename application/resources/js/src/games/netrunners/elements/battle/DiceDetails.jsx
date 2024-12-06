import {configNetrunners} from "../../configNetrunners.jsx";
import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {PowerDetails} from "./PowerDetails.jsx";

export const DiceDetails = ({player, addClass}) => {

    console.log('DiceDetails', player);

    const dices = useNetrunnersStore(state => state.situation.battle.dices);

    const styleImage = {
        backgroundImage: configNetrunners.covers.diceS,
    };

    const classDiv = ' h-[70%] bg-center bg-no-repeat bg-cover aspect-square rounded-lg mr-[2vh] border-[0.4vh] border-solid '
        + ' border-neutral-500 ';
    //
    // + (yourTurn ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg hover:cursor-pointer ' : ' border-cyan-500 shadow-actionSmOp ')
    //     + addClass;

    return (
        <div className={addClass}>
            <div className='flex h-full items-center justify-center'>
                <div className={classDiv} style={styleImage}></div>
            </div>
            <PowerDetails power={`${dices[0]} | ${dices[1]}`} addClass=' text-[3.5vh] -mr-[4vh] ' />
            <PowerDetails power={dices[0] + dices[1]}/>
        </div>
    );
}
