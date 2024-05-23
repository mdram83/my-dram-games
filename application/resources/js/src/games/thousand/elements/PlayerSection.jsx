import React from "react";
import {Avatar} from "./Avatar.jsx";
import {PlayerInfoBidding} from "./PlayerInfoBidding.jsx";

export const PlayerSection = ({fourPlayersGame}) => {

    console.log('call PlayerSection');
    const playerName = window.MyDramGames.player.name;

    // TODO continue here, with player hand + show to Player who is fourPlayerDealer that he is a dealer

    return (
        <div className="flex flex-row items-center h-full">

            <div className="basis-[20%] sm:basis-[10%]">
                <Avatar playerName={playerName}/>
            </div>

            <div className="basis-[60%] sm:basis-[80%]">

                <div className="absolute bottom-[12vh] left-0 w-full">
                    <div className="flex justify-center">
                        <PlayerInfoBidding playerName={playerName} fourPlayersGame={fourPlayersGame} />
                    </div>
                </div>

                <div>Tricks</div>
                <div>Hand</div>
            </div>

            <div className="basis-[20%] sm:basis-[10%]">
                Hand view switch
            </div>

        </div>
    );
}
