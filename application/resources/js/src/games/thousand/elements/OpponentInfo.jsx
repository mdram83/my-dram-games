import React from "react";
import {OpponentInfoDealer} from "./OpponentInfoDealer.jsx";
import {OpponentInfoBidding} from "./OpponentInfobBidding.jsx";
import {useThousandStore} from "../useThousandStore.jsx";

export const OpponentInfo = ({playerName, fourPlayersGame}) => {

    console.log('OpponentInfo:' + playerName);

    const dealer = useThousandStore(state => state.situation.dealer);
    const isDealer = playerName === dealer;
    const isFourPlayersDealer = isDealer && fourPlayersGame;

    const singleClassName = 'flex justify-center items-center my-[1vh]';

    return (
        <div>

            <div className={singleClassName}>
                <OpponentInfoDealer playerName={playerName} isFourPlayersDealer={isFourPlayersDealer}/>
            </div>

            <div className={singleClassName}>
                <OpponentInfoBidding playerName={playerName} isFourPlayersDealer={isFourPlayersDealer}/>
            </div>

        </div>
    );
}
