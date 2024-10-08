import React, {useEffect} from "react";
import axios from "axios";
import {SiteButton} from "../../../template/components/SiteButton.jsx";
import {GameInvitePlayers} from "./GameInvitePlayers.jsx";
import {FlashMessage} from "../../../template/components/FlashMessage.jsx";

export const GameInviteShow = ({gameInvite, slug, gamePlayId = undefined, gameRecords = undefined}) => {

    const isPlayerHost = gameInvite.host.name === window.MyDramGames.player.name;
    const [allPlayersOnline, setAllPlayersOnline] = React.useState(false);
    const [errorMessage, setErrorMessage] = React.useState(undefined);
    const [recordsModule, setRecordsModule] = React.useState(undefined);

    const joinUrl = window.MyDramGames.routes["game-invites.join-redirect"](slug, gameInvite.id);

    const copyJoinUrl = () => navigator.clipboard.writeText(joinUrl);
    const showGamePlay = (gamePlayId) => window.location.href = window.MyDramGames.routes["gameplay.show"](gamePlayId);

    const storeGamePlay = () => {
        axios.post(window.MyDramGames.routes["ajax.gameplay.store"], {gameInviteId: gameInvite.id})
            .then(() => { })
            .catch(error => setErrorMessage(error.response.data.message.message ?? 'Unexpected error'));
    }

    useEffect(() => {
        if (gameRecords) {
            import(`../../games/${slug}/GameRecords.jsx`).then((module) => setRecordsModule(module.default(gameRecords)));
        }
    }, [])

    useEffect(() => {
        const autostartOption = gameInvite.options.autostart ?? 0;
        if (autostartOption && allPlayersOnline && !gamePlayId) {
            storeGamePlay();
        }
    }, [allPlayersOnline]);

    return (
        <div className="text-white">

            {(errorMessage !== undefined) && <FlashMessage message={errorMessage} isError={true} />}

            <h4 className="font-bold font-sans mb-4">Game Settings</h4>

            <GameInvitePlayers gameInvite={gameInvite}
                               setAllPlayersOnline={(ready) => setAllPlayersOnline(ready)}
                               autoStart={(gamePlayId) => showGamePlay(gamePlayId)}
            />

            <div className="w-full sm:w-auto flex justify-center sm:justify-start">

                {isPlayerHost && allPlayersOnline && !gamePlayId && <SiteButton value='Start'
                                                                                onClick={() => storeGamePlay()}
                                                                                className='mr-2'
                                                                                faClassName='fa-play'/>
                }

                {gamePlayId && !gameRecords && <SiteButton value='Resume'
                                                           onClick={() => showGamePlay(gamePlayId)}
                                                           className='mr-2'
                                                           faClassName='fa-play' />
                }

                <SiteButton value='Copy Link' onClick={() => copyJoinUrl()} faClassName='fa-link' />

            </div>

            {recordsModule &&
                <div>
                    <h4 className="font-bold font-sans my-4">Game Records</h4>
                    {recordsModule}
                </div>
            }

        </div>
    );
}
