import React from "react";
import {SiteButton} from "../../components/SiteButton.jsx";
import {NewGamePlayers} from "./NewGamePlayers.jsx";
import axios from "axios";

export const NewGameSummary = ({gameInvite, slug}) => {

    const joinUrl = window.MyDramGames.routes["game-invites.join"](slug, gameInvite.id);

    const startGame = () => {
        axios.post(window.MyDramGames.routes["ajax.gameplay.store"], {gameInviteId: gameInvite.id})
            .then(response => { })
            .catch(error =>  console.log(error));
    }
    const play = () => window.location.href = window.MyDramGames.routes["gameplay.show"](gameInvite.id);
    const joinUrlCopy = () => navigator.clipboard.writeText(joinUrl);
    const isCurrentPlayerHost = gameInvite.host.name === window.MyDramGames.player.name;

    const [allPlayersReady, setAllPlayersReady] = React.useState(false);

    return (
        <div className="text-white">

            <h4 className="font-bold font-sans mb-4">Game Settings</h4>
            <NewGamePlayers gameInvite={gameInvite} setAllPlayersReady={(ready) => setAllPlayersReady(ready)} autoStart={() => play()} />

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
