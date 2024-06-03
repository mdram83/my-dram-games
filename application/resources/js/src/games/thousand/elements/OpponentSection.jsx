import React from "react";
import {Avatar} from "./Avatar.jsx";
import {OpponentHand} from "./OpponentHand.jsx";
import {OpponentTricks} from "./OpponentTricks.jsx";
import {OpponentInfo} from "./OpponentInfo.jsx";
import {OpponentStockDistribution} from "./OpponentStockDistribution.jsx";

export const OpponentSection = ({playerName, fourPlayersGame}) => {

    console.log('OpponentSection:' + playerName);
    const singleClassName = 'flex justify-center items-center';

    return (
        <div>

            <div className={singleClassName}>
                <OpponentInfo playerName={playerName} fourPlayersGame={fourPlayersGame} />
            </div>

            <div className={singleClassName}>
                <Avatar playerName={playerName}/>
            </div>

            <div className={singleClassName}>
                <OpponentHand playerName={playerName}/>
            </div>

            <div className={singleClassName}>
                <OpponentStockDistribution playerName={playerName} fourPlayersGame={fourPlayersGame} />
            </div>

            <div className={singleClassName}>
                <OpponentTricks playerName={playerName}/>
            </div>

        </div>
    );
}
