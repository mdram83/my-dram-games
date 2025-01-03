import React from "react";
import {DiceDetails} from "./DiceDetails.jsx";
import {HardwareDetails} from "./HardwareDetails.jsx";
import {SoftwareDetails} from "./SoftwareDetails.jsx";
import {AbilityDetails} from "./AbilityDetails.jsx";

export const BattleDetails = ({player}) => {

    const classBattleLine = ' flex h-full w-[95%] justify-between items-center ';

    return (
        <div className='grid grid-rows-4 h-full'>
            <DiceDetails player={player} addClass={classBattleLine} />
            <HardwareDetails player={player} addClass={classBattleLine} />
            <SoftwareDetails player={player} addClass={classBattleLine} />
            <AbilityDetails player={player} addClass={classBattleLine} />
        </div>
    );
}
