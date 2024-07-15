import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {minicardsSuitsSvg} from "../../../../template/elements/playing-cards/decks/minicardsSuitsSvg.jsx";

export const TrumpSuit = () => {

    const trumpSuit = useThousandStore(state => state.situation.trumpSuit);

    const getSuit = (suitKey) => {
        return minicardsSuitsSvg[suitKey];
    }

    const renderTrumpSuit = () => {
        if (trumpSuit === null) {
            return <></>;
        }

        return (
            <div className="flex items-center px-[1.5vh] py-[1.2vh] border-[0.4vh] border-solid border-gray-400 bg-white rounded-md bg-opacity-60 shadow-md shadow-gray-600">
                <span className="font-sans text-gray-700 font-bold text-[3vh] pr-[1vh] pb-[0.2vh]">TRUMP</span>
                {getSuit(trumpSuit)}
            </div>
        );

    }

    return renderTrumpSuit();
}
