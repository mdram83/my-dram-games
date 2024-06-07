import React from "react";
import {PlayerInfoDeclaration} from "./PlayerInfoDeclaration.jsx";
import {PlayerInfoStockDistribution} from "./PlayerInfoStockDistribution.jsx";
import {PlayerInfoBidding} from "./PlayerInfoBidding.jsx";

export const PlayerInfos = () => {

    console.log('PlayerInfos');

    return (
        <div className="absolute bottom-[2vh] mr-[4vh] sm:w-[60vw] w-[40vw] max-w-[400px] min-w-[20vw]">
            <PlayerInfoBidding playerName={window.MyDramGames.player.name} />
            <PlayerInfoStockDistribution />
            <PlayerInfoDeclaration />
        </div>
    );
}
