import React from "react";
import {useEffect} from "react";

export const NewGamePlayers = ({game, currentPlayerName, setAllPlayersReady}) => {

    const {players} = game;
    const initialPlayersStatus = players.map((player) => {
        return {
            name: player.name,
            host: player.name === game.host.name,
            connected: player.name === currentPlayerName,
        }
    });

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
                    host: playerName === game.host.name,
                    connected: connected,
                });
            }

            return newPlayersStatus;
        });
    }

    useEffect(() => {
        Echo.join(`game.${game.id}`)
            .here((users) => users.forEach((user) => updatePlayerStatus(user.name, true)))
            .joining((user) => updatePlayerStatus(user.name, true))
            .leaving((user) => updatePlayerStatus(user.name, false))
            .error((error) => console.log(error));
    }, []);

    useEffect(() => {
        setAllPlayersReady(
            playersStatus.length === game.numberOfPlayers && playersStatus.every((player) => player.connected)
        );
    });

    const renderPlayersList = () => {

        const listItems = [];

        for (let i = 0; i < game.numberOfPlayers; i++ ) {

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
            <div className="mb-4">Players Status</div>

            <div className='text-white text-sm mb-6 ml-2'>
                <ul className='list-none'>
                    {renderPlayersList()}
                </ul>
            </div>
        </>
    );
}
