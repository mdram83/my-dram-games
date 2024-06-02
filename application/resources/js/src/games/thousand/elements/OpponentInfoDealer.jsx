import React from "react";

export const OpponentInfoDealer = ({playerName, isFourPlayersDealer}) => {

    console.log('OpponentInfoDealer:' + playerName);

    return (
        <div>
            {isFourPlayersDealer && <span className="font-sans text-blue-800 font-bold text-[3vh]">DEALER</span>}
            {!isFourPlayersDealer && <span>&nbsp;</span>}
        </div>
    );

}
