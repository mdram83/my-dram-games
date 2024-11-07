import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {BatteryPoint} from "./BatteryPoint.jsx";

export const Battery = ({playerName, targetPoints = undefined}) => {

    console.log('Battery', playerName, targetPoints);

    const points = useNetrunnersStore(state => state.situation.players[playerName].battery);
    targetPoints = targetPoints === undefined ? points : targetPoints;

    const renderPoints = () => {
        const elements = new Array(5);
        for (let i = 0; i < Math.max(points, targetPoints); i++) {
            elements.push(<BatteryPoint key={i} order={i + 1} points={points} targetPoints={targetPoints} />);
        }
        return elements;
    }

    return (
        <div className='flex items-center justify-center size-full'>
            <div className='grid grid-cols-12 items-center w-[80%] h-[80%]'>

                {/*BLOCK*/}
                <div className='flex items-center justify-center col-span-11 rounded-[1vh] h-full border-[0.5vh] border-solid border-white'>
                    <div className='grid grid-cols-5 gap-[1.4vh] items-center w-[90%] h-[80%]'>
                        {renderPoints()}
                    </div>
                </div>

                {/*CATHODE*/}
                <div className='flex items-center justify-start h-full'>
                    <div className='h-[35%] w-[20%] rounded-r-[0.5vh] border-[0.5vh] border-solid border-white bg-white'></div>
                </div>

            </div>
        </div>
    );
}
