import React, {useState} from "react";

export const GamePoints = () => {

    console.log('GamePoints');

    const [display, setDisplay] = useState(false);
    const toggleDisplay = () => setDisplay(!display);

    const renderGamePoints = () => {

        const bgUrl = window.MyDramGames["asset-url"].img + '/core/elements/backgrounds/pexels-lum3n-44775-316466.jpg';
        const toggleClassName = display
            ? ' w-[92vw] sm:w-[93.2vw] h-[66vh] sm:h-[62vh] shadow-sm border-gray-800'
            : ' shadow-md border-gray-400 shadow-gray-400 ';
        const divClassName =
            ' bg-center bg-no-repeat bg-cover '
            + ' flex items-center px-[1.5vw] py-[1.2vh] border-[0.4vh] cursor-pointer '
            + ' border-solid rounded-md '
            + toggleClassName;

        const label = display
            ? null
            : <span className="font-sans text-gray-700 font-bold text-[3vh] px-[1vh] py-[0.2vh]">POINTS</span>

        return (
            <div className={divClassName} style={{backgroundImage: `url(${bgUrl})`}} onClick={toggleDisplay}>
                {label}
            </div>
        );
    }

    return renderGamePoints();
}
