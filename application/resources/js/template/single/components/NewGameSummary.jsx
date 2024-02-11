import React from "react";
import {SiteButton} from "../../components/SiteButton.jsx";
import {NewGamePlayers} from "./NewGamePlayers.jsx";
import axios from "axios";

export const NewGameSummary = ({game, joinUrlBase, playUrlBase, currentPlayerName}) => {

    const joinUrl = joinUrlBase + '/' + game.id;
    const playUrl = playUrlBase + '/' + game.id;
    const startGame = () => {
        // window.location.assign(playUrl);
        axios
            .get(playUrl, {})
            .then(response => {
                console.log(response);
            })
            .catch(error => {
                console.log(error);
            });
    }
    const autoStart = () => window.location.assign(playUrl);
    const joinUrlCopy = () => navigator.clipboard.writeText(joinUrl);
    const isCurrentPlayerHost = game.host.name === currentPlayerName;

    const [allPlayersReady, setAllPlayersReady] = React.useState(false);

    return (
        <div className="text-white">

            <h4 className="font-bold font-sans mb-4">Game Settings</h4>

            <NewGamePlayers game={game}
                            currentPlayerName={currentPlayerName}
                            setAllPlayersReady={(ready) => setAllPlayersReady(ready)}
                            autoStart={() => startGame()}
            />

            <div className="w-full sm:w-auto flex justify-center sm:justify-start">

                {
                    isCurrentPlayerHost &&
                    allPlayersReady &&
                    <SiteButton value='Start' onClick={() => startGame()} className='mr-2' faClassName='fa-play' />
                }

                <SiteButton value='Copy Link' onClick={() => joinUrlCopy()} faClassName='fa-link' />

            </div>

            {/*TEMP SECTION*/}
            <div className="w-full sm:w-auto flex justify-center sm:justify-start mt-4">
                <SiteButton value='Tmp Join Link' onClick={() => window.location.href = joinUrl} className='mr-2' />
            </div>

        </div>
    );
}
