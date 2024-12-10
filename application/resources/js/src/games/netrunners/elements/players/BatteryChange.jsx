import React from "react";
import {Battery} from "../misc/Battery.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const BatteryChange = ({playerName}) => {

    console.log('BatteryChange');

    const points = useNetrunnersStore(state => state.situation.players[playerName].battery);

    // TODO effect to show it for x seconds after the change and then hide.
    const targetPoints = useNetrunnersStore(state => state.situation.players[playerName].battery);


    return (

        <div className="absolute size-[80%]">
            <div className='flex items-center justify-center size-full'>

                <div className='w-full h-[60%] aspect-square '>
                    <Battery points={points} targetPoints={targetPoints} smallSize={true}/>
                </div>

            </div>
        </div>

    );
}
