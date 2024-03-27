import React from "react";
import {useEffect} from "react";
import {FlashMessage} from "../../../template/components/FlashMessage.jsx";

export const GameInvitePlayers = ({gameInvite, setAllPlayersOnline, autoStart}) => {

    const initialPlayersStatus = gameInvite.players.map((player) => {
        return {
            name: player.name,
            host: player.name === gameInvite.host.name,
            connected: false,
        }
    });

    const [errorMessage, setErrorMessage] = React.useState(undefined);
    const [playersStatus, setPlayersStatus] = React.useState(initialPlayersStatus);

    const updatePlayerStatus = (playerName, connected) => {
        setPlayersStatus((previousPlayersStatus) => {

            let existingPlayer = false;

            const newPlayersStatus = previousPlayersStatus.map((player) => {
                if (playerName === player.name) {
                    existingPlayer = true;
                    return {...player, connected: connected};
                } else {
                    return player;
                }
            });

            if (!existingPlayer) {
                newPlayersStatus.push({
                    name: playerName,
                    host: playerName === gameInvite.host.name,
                    connected: connected,
                });
            }

            return newPlayersStatus;
        });
    }

    useEffect(() => {

        Echo.join(`game-invite-players.${gameInvite.id}`)
            .here((users) => users.forEach((user) => updatePlayerStatus(user.name, true)))
            .joining((user) => updatePlayerStatus(user.name, true))
            .leaving((user) => updatePlayerStatus(user.name, false))
            .listen('GameCore\\GamePlay\\GamePlayStoredEvent', (e) => autoStart(e.gamePlayId))
            .error((error) => setErrorMessage(error.status === 403 ? 'Authentication error' : 'Unexpected error'));

    }, []);

    useEffect(() => {
        setAllPlayersOnline(
            playersStatus.length === gameInvite.options.numberOfPlayers && playersStatus.every((player) => player.connected)
        );
    });

    const renderPlayersList = () => {

        const listItems = [];

        for (let i = 0; i < gameInvite.options.numberOfPlayers; i++ ) {

            const playerName = playersStatus[i] ? playersStatus[i].name : undefined;
            const playerHost = playersStatus[i] ? playersStatus[i].host : false;
            const playerConnected = playersStatus[i] ? playersStatus[i].connected : false;
            const iconColor = playerName === undefined ? 'gray' : (playerConnected ? 'green' : 'red');

            listItems.push(
                <li key={'player-' + i} className='flex items-center'>
                    <span className='flex justify-center' style={{color: iconColor, width: '20px'}}>
                        <i className={'fa ' + (playerHost ? 'fa-id-badge' : 'fa-user') + ' content-center mr-2'}></i>
                    </span>
                    <span className='font-semibold'>{playerName ?? '[ free slot ]'}</span>
                </li>
            );
        }

        return listItems;
    }

    return (
        <>
            {(errorMessage !== undefined) && <FlashMessage message={errorMessage} isError={true} />}

            <div className="mb-4">Players Status</div>

            <div className='text-white text-sm mb-6 ml-2'>
                <ul className='list-none'>
                    {renderPlayersList()}
                </ul>
            </div>
        </>
    );
}
