import React from "react";
import {Avatar} from "./Avatar.jsx";
import {useThousandStore} from "../useThousandStore.jsx";
import {PlayerFourPlayersDealerMessage} from "./PlayerFourPlayersDealerMessage.jsx";
import {OpponentTricks} from "./OpponentTricks.jsx";

export const PlayerSection = ({fourPlayersGame}) => {

    console.log('PlayerSection');
    const playerName = window.MyDramGames.player.name;

    const dealer = useThousandStore(state => state.situation.dealer);
    const isDealer = playerName === dealer;
    const isFourPlayersDealer = isDealer && fourPlayersGame;

    return (
        <div className="flex flex-row items-center h-full">

            <div className="basis-[20%] sm:basis-[10%]">
                <Avatar playerName={playerName}/>
                <div className="flex justify-center items-center">
                    <div className="static">
                        <div className="absolute bottom-[16vh] sm:bottom-[24vh] left-[7vw] sm:left-[2vw] ">
                            <OpponentTricks playerName={playerName}/>
                        </div>
                    </div>
                </div>
            </div>

            {isFourPlayersDealer &&
                <div className="basis-[80%] sm:basis-[90%] flex justify-center">
                <PlayerFourPlayersDealerMessage />
            </div>
            }

        </div>
    );
}
