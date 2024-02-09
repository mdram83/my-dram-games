import React from "react";
import {SiteButton} from "../../components/SiteButton.jsx";
import {NewGamePlayers} from "./NewGamePlayers.jsx";

export const NewGameSummary = ({game, joinUrlBase, currentPlayerName}) => {

    const joinUrl = joinUrlBase + '/' + game.id;
    const startGame = () => window.location.href = joinUrl;
    const joinUrlCopy = () => navigator.clipboard.writeText(joinUrl);
    const isCurrentPlayerHost = game.host.name === currentPlayerName;

    return (
        <div className="text-white">

            <h4 className="font-bold font-sans mb-4">Game Settings</h4>

            <NewGamePlayers game={game} currentPlayerName={currentPlayerName} />

            <div className="w-full sm:w-auto flex justify-center sm:justify-start">
                {isCurrentPlayerHost && <SiteButton value='Start' onClick={() => startGame()} className='mr-2' faClassName='fa-play' />}
                <SiteButton value='Copy Link' onClick={() => joinUrlCopy()} faClassName='fa-link' />
            </div>

        </div>
    );
}
