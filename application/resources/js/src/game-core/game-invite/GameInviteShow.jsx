import React from "react";
import {SiteButton} from "../../../template/components/SiteButton.jsx";
import {GameInvitePlayers} from "./GameInvitePlayers.jsx";
import axios from "axios";
import {FlashMessage} from "../../../template/components/FlashMessage.jsx";

export const GameInviteShow = ({gameInvite, slug}) => {

    const isPlayerHost = gameInvite.host.name === window.MyDramGames.player.name;
    const [allPlayersOnline, setAllPlayersOnline] = React.useState(false);
    const [errorMessage, setErrorMessage] = React.useState(undefined);
    const joinUrl = window.MyDramGames.routes["game-invites.join"](slug, gameInvite.id);

    const copyJoinUrl = () => navigator.clipboard.writeText(joinUrl);
    const showGamePlay = () => window.location.href = window.MyDramGames.routes["gameplay.show"](gameInvite.id);

    const storeGamePlay = () => {
        axios.post(window.MyDramGames.routes["ajax.gameplay.store"], {gameInviteId: gameInvite.id})
            .then(response => { })
            .catch(error => setErrorMessage(error.response.data.message.message ?? 'Unexpected error'));
    }

    return (
        <div className="text-white">

            {(errorMessage !== undefined) && <FlashMessage message={errorMessage} isError={true} />}

            <h4 className="font-bold font-sans mb-4">Game Settings</h4>

            <GameInvitePlayers gameInvite={gameInvite}
                               setAllPlayersOnline={(ready) => setAllPlayersOnline(ready)}
                               autoStart={() => showGamePlay()}
            />

            <div className="w-full sm:w-auto flex justify-center sm:justify-start">

                {isPlayerHost && allPlayersOnline && <SiteButton value='Start'
                                                                 onClick={() => storeGamePlay()}
                                                                 className='mr-2'
                                                                 faClassName='fa-play'
                />}
                <SiteButton value='Copy Link' onClick={() => copyJoinUrl()} faClassName='fa-link' />

            </div>

            {/*TEMP SECTION*/}
            <div className="w-full sm:w-auto flex justify-center sm:justify-start mt-4">
                <SiteButton value='Tmp Join Link' onClick={() => window.location.href = joinUrl} className='mr-2' />
            </div>

        </div>
    );
}
