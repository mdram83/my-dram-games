import React from "react";
import {Avatar} from "./Avatar.jsx";
import {PlayerInfoBidding} from "./PlayerInfoBidding.jsx";
import {useThousandStore} from "../useThousandStore.jsx";
import {PlayerFourPlayersDealerMessage} from "./PlayerFourPlayersDealerMessage.jsx";
import {PlayerHand} from "./PlayerHand.jsx";

export const PlayerSection = ({fourPlayersGame}) => {

    console.log('call PlayerSection');
    const playerName = window.MyDramGames.player.name;

    const dealer = useThousandStore(state => state.situation.dealer);
    const isDealer = playerName === dealer;
    const isFourPlayersDealer = isDealer && fourPlayersGame;

    // TODO continue here, with player hand

    return (
        <div className="flex flex-row items-center h-full">

            <div className="basis-[20%] sm:basis-[10%]">
                <Avatar playerName={playerName}/>
            </div>

            {isFourPlayersDealer &&
                <div className="basis-[80%] sm:basis-[90%] flex justify-center">
                    <PlayerFourPlayersDealerMessage />
                </div>
            }

            {!isFourPlayersDealer && <div className="basis-[60%] sm:basis-[80%]">

                <div className="absolute bottom-[12vh] left-0 w-full">
                    <div className="flex justify-center">
                        <PlayerInfoBidding playerName={playerName} />
                    </div>
                </div>

                <div>Tricks</div>
                <div><PlayerHand playerName={playerName} /></div>
            </div>}

            {!isFourPlayersDealer && <div className="basis-[20%] sm:basis-[10%]">
                Hand view switch
            </div>}

        </div>
    );
}
