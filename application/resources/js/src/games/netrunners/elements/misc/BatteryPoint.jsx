import React from "react";

export const BatteryPoint = ({order, points, targetPoints}) => {

    console.log('BatteryPoint', order, points, targetPoints);

    let classDiv = ' rounded-[0.5vh] size-full ';

    switch (true) {
        case (points < targetPoints && order > points):
            classDiv += ' bg-green-500 animate-pulse ';
            break;
        case (points > targetPoints && order > targetPoints):
            classDiv += ' bg-red-500 animate-pulseFast ';
            break;
        default:
            classDiv += ' bg-green-500 ';
            break;
    }

    return <div className={classDiv}></div>;
}
