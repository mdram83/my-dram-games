import React, {useEffect, useState} from "react";
import {GamePoints} from "./GamePoints.jsx";
import {useThousandStore} from "../useThousandStore.jsx";
import {GameDetails} from "./GameDetails.jsx";
import {GameResult} from "./GameResult.jsx";

export const GameInformation = () => {

    const playerName = window.MyDramGames.player.name;
    const ready = useThousandStore(state => state.situation.orderedPlayers[playerName].ready);

    const isFinished = useThousandStore(state => state.situation.isFinished);
    const pointsSpanClassName = isFinished ? 'col-span-4 sm:col-span-4' : 'col-span-4 sm:col-span-3';

    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseCountPoints = phaseKey === 'counting-points';
    const isPhaseBidding = phaseKey === 'bidding';

    const [display, setDisplay] = useState(isPhaseCountPoints);
    const toggleDisplay = () => setDisplay(!display);

    useEffect(() => {
        setDisplay((isPhaseCountPoints || display) && !isPhaseBidding);
    }, [isPhaseCountPoints, isPhaseBidding]);

    useEffect(() => {
        setDisplay(!ready);
    }, [ready]);


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
                    <div className='grid grid-cols-4 gap-1 w-full'>
                        <div className={pointsSpanClassName}><GamePoints /></div>
                        {!isFinished && <div className='col-span-4 sm:col-span-1'><GameDetails /></div>}
                        {isFinished && <div className='col-span-4'><GameResult /></div>}
                    </div>
                }
            </div>
        );
    }

    return renderGamePoints();
}
