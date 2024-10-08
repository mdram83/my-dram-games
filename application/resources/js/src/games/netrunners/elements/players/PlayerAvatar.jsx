import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const PlayerAvatar = ({playerName, character = undefined}) => {

    console.log('players/Avatar', playerName, character);

    const display = useNetrunnersStore(state => state.playerInfoScreen.display);
    const displayPlayerName = useNetrunnersStore(state => state.playerInfoScreen.playerName);
    const setPlayerInfoScreen = useNetrunnersStore(state => state.setPlayerInfoScreen);

    const isConnected = useGamePlayStore((state) => state.players[playerName]);
    const activePlayer = useGamePlayStore((state) => state.activePlayer);
    const isActivePlayer = activePlayer === playerName;

    const isYou = playerName === MyDramGames.player.name;
    const initials = playerName.charAt(0).toUpperCase();

    const style = {
        backgroundImage: character ? configNetrunners.characters[character].imageAvatarS : '',
        filter: isActivePlayer ? 'grayscale(0)' : 'grayscale(100%)',
    };

    const classDivCircle =
        ' relative flex items-center justify-center w-[8vh] sm:w-[10vh] h-[8vh] sm:h-[10vh] rounded-full bg-gray-900 '
        + (isYou ? ' border-[0.5vh] border-solid ' : ' border-[0.4vh] border-dotted')
        + (character ? configNetrunners.characters[character].classAvatarBorder : ' border-lime-600 ')
        + (character ? ' cursor-pointer ' : ' ')
        + (isActivePlayer ? ' animate-pulse ' : ' ')
        + (isActivePlayer && !character ? ' bg-white ' : ' bg-gray-200 ');

    const onClick = () => {
        if (character) {

            if (display) {
                setPlayerInfoScreen(displayPlayerName !== playerName, displayPlayerName === playerName ? undefined : playerName);
            } else {
                setPlayerInfoScreen(true, playerName);
            }

        } else {
            setPlayerInfoScreen(false);
        }
    }

    return (
        <div className={classDivCircle} onClick={onClick}>

            <div className='flex items-center justify-center bg-bottom bg-no-repeat bg-cover w-full h-full rounded-full'
                 style={style}
            >
                {
                    !character &&
                    <div className='text-[4vh] sm:text-[5vh] font-sans pb-[0.5vh] text-lime-600 font-bold'>
                        {initials}
                    </div>
                }
            </div>

            {
                !isConnected &&
                <div className="absolute pt-[0.5vh]">
                    <i className="fa fa-wifi text-[4.0vh] text-red-600 animate-ping"></i>
                </div>
            }

        </div>
    );
}
