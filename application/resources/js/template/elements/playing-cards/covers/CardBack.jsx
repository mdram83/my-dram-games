import React from "react";

export const CardBack = () => {

    const cardBackUrl = window.MyDramGames["asset-url"].img
        + '/core/elements/decks/playing-cards/card-7031432_640.png';

    return (
        <div>
            <img className="w-[5vh] h-[7vh] sm:w-[10vh] sm:h-[14vh]" width="auto" height="auto" alt='' src={cardBackUrl}/>
        </div>
    );
}
