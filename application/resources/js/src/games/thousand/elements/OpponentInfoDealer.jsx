import React from "react";

export const OpponentInfoDealer = ({playerName, isFourPlayersDealer}) => {

    console.log('  call OpponentInfoDealer for ' + playerName);

    return (
        <div>
            {isFourPlayersDealer && <span className="font-sans text-blue-800 font-bold">DEALER</span>}
            {!isFourPlayersDealer && <span>&nbsp;</span>}
        </div>
    );

}
