import React from "react";
import {BatteryPoint} from "./BatteryPoint.jsx";

export const Battery = ({points, targetPoints = undefined, smallSize = false}) => {

    targetPoints = targetPoints === undefined ? points : targetPoints;

    const renderPoints = () => {
        const elements = new Array(5);
        for (let i = 0; i < Math.max(points, targetPoints); i++) {
            elements.push(<BatteryPoint key={i} order={i + 1} points={points} targetPoints={targetPoints} classAdd={classPoint} />);
        }
        return elements;
    }

    const classBlock = ' flex items-center justify-center col-span-11 h-full border-solid border-white '
        + (!smallSize ? ' rounded-[1vh] border-[0.5vh] ' : ' rounded-[0.75vh] border-[0.4vh] ');

    const classBlockGrid = ' grid grid-cols-5 items-center w-[90%] h-[80%] '
        + (!smallSize ? ' gap-[5%] ' : ' gap-[4%] ');

    const classCathode = ' h-[35%] w-[20%] border-solid border-white bg-white '
        + (!smallSize ? ' rounded-r-[0.5vh] border-[0.5vh] ' : ' rounded-r-[0.25vh] border-[0.4vh] ');

    const classPoint = !smallSize ? ' rounded-[0.5vh] ' : ' rounded-[0.1vh] ';

    return (
        <div className='flex items-center justify-center size-full'>
            <div className='grid grid-cols-12 items-center w-[80%] h-[80%]'>

                {/*BLOCK*/}
                <div className={classBlock}>
                    <div className={classBlockGrid}>
                        {renderPoints()}
                    </div>
                </div>

                {/*CATHODE*/}
                <div className='flex items-center justify-start h-full'>
                    <div className={classCathode}></div>
                </div>

            </div>
        </div>
    );
}
