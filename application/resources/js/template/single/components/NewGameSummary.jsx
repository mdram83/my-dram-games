import React from "react";
import {SiteButton} from "../../components/SiteButton.jsx";

export const NewGameSummary = ({game, joinUrlBase, currentPlayerName}) => {

    const joinUrl = joinUrlBase + '/' + game.id;
    const startGame = () => window.location.href = joinUrl;
    const joinUrlCopy = () => navigator.clipboard.writeText(joinUrl);

    const isCurrentPlayerHost = game.host.name === currentPlayerName;

    const {players} = game;
    const playersList = [];
    for (let i = 0; i < game.numberOfPlayers; i++) {

        const player = players[i] ?? undefined;
        const connected = !!player; // TODO to be adjusted later with websocket connection status

        const iconColor = (!player ? 'red' : (connected ? 'green' : 'gray'));
        const iconClass = (!player ? 'fa-bolt' : (connected ? 'fa-check' : 'fa-spinner fa-spin'));
        const playerName = player ? player.name : '';
        const playerRole = !player ? '[ available ] ' : (i === 0 ? '[ host ] ' : '');

        playersList.push(
            <li key={'player-' + i} className='flex items-center'>
                <span className='flex justify-center' style={{color: iconColor, width: '20px'}}>
                    <i className={'fa ' + iconClass + ' content-center mr-2'}></i>
                </span>
                <span className='mr-1' style={{ color: '#bbb', fontWeight: '100' }}>{playerRole}</span>
                <span className='font-semibold'>{playerName}</span>
            </li>
        );
    }

    return (
        <div className="text-white">

            <h4 className="font-bold font-sans mb-4">Game Settings</h4>

            <div className="mb-2">
                <i className="fa fa-users text-white mr-2 content-center"></i>
                Players Status
            </div>

            <div className='text-white text-sm mb-4 ml-4'>
                <ul className='list-none'>
                    {playersList}
                </ul>
            </div>

            <div className="w-full sm:w-auto flex justify-center sm:justify-start">
                {isCurrentPlayerHost && <SiteButton value='Start' onClick={() => startGame()} className='mr-2' faClassName='fa-play' />}
                <SiteButton value='Copy Link' onClick={() => joinUrlCopy()} faClassName='fa-link' />
            </div>

        </div>
    );
}
