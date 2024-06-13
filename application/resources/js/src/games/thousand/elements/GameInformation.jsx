import React, {useState} from "react";
import {GamePoints} from "./GamePoints.jsx";

export const GameInformation = () => {

    console.log('GameInformation');

    const [display, setDisplay] = useState(false);
    const toggleDisplay = () => setDisplay(!display);

    const renderGamePoints = () => {

        const bgUrl = window.MyDramGames["asset-url"].img + '/core/elements/backgrounds/pexels-lum3n-44775-316466.jpg';
        const toggleClassName = display
            ? ' w-[92vw] sm:w-[93.2vw] h-[66vh] sm:h-[62vh] shadow-sm border-gray-800'
            : ' shadow-md border-gray-400 shadow-gray-600 flex items-center ';
        const divClassName =
            ' bg-center bg-no-repeat bg-cover '
            + ' px-[1.5vw] py-[1.2vh] border-[0.4vh] cursor-pointer '
            + ' border-solid rounded-md '
            + toggleClassName;

        const label = display
            ? null
            : <span className="font-sans text-gray-700 font-bold text-[3vh] px-[1vh] py-[0.2vh]">POINTS</span>

        return (
            <div className={divClassName} style={{backgroundImage: `url(${bgUrl})`}} onClick={toggleDisplay}>
                {label}
                {display &&
                    <div className='grid grid-cols-2 gap-1 w-full'>
                        <div className='col-span-2 sm:col-span-1'><GamePoints /></div>
                        <div className='col-span-2 sm:col-span-1'>Game Details</div>
                    </div>
                }
            </div>
        );
    }

    return renderGamePoints();
}
