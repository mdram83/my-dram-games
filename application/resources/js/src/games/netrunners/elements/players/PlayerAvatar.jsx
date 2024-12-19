import React, {useEffect, useRef, useState} from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {BatteryChange} from "./BatteryChange.jsx";

export const PlayerAvatar = ({playerName, character = undefined}) => {

    console.log('players/Avatar', playerName, character);

    const display = useNetrunnersStore(state => state.playerInfoScreen.display);
    const displayPlayerName = useNetrunnersStore(state => state.playerInfoScreen.playerName);
    const setPlayerInfoScreen = useNetrunnersStore(state => state.setPlayerInfoScreen);
    const canSwitchMapLocation = useNetrunnersStore(state => state.situation.canSwitchMapLocation);
    const isPhaseHack = useNetrunnersStore(state => state.isPhaseHack);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);
    const battery = useNetrunnersStore(state => state.situation.players[playerName].battery);

    const actionablePlayer = canSwitchMapLocation || isPhaseHack;
    const yourActionablePlayer = actionablePlayer && yourTurn;

    const isConnected = useGamePlayStore((state) => state.players[playerName]);
    const activePlayer = useGamePlayStore((state) => state.activePlayer);
    const isActivePlayer = activePlayer === playerName;

    const isYou = playerName === MyDramGames.player.name;
    const initials = playerName.charAt(0).toUpperCase();


    const [glitch, setGlitch] = useState(false);

    const interval = useRef(undefined);
    const timeout = useRef(undefined);

    useEffect(() => {

        if (battery === 0) {

            interval.current = setInterval(() => {
                setGlitch(true);
                timeout.current = setTimeout(() => {
                    setGlitch(false);
                }, Math.floor(Math.random() * 5) * 100);
            }, 1600);

        } else {
            setGlitch(false);
            clearInterval(interval.current);
            clearTimeout(timeout.current);
        }

        return () => {
            clearInterval(interval.current);
            clearTimeout(timeout.current);
        }

    }, [battery]);


    const style = {
        backgroundImage: character ? configNetrunners.characters[character].imageAvatarS : '',
        filter: isActivePlayer ? 'grayscale(0)' : 'grayscale(100%)',
    };

    const classDivCircle =
        ' relative flex items-center justify-center w-[8vh] sm:w-[10vh] h-[8vh] sm:h-[10vh] rounded-full bg-gray-900 '
        + (isYou ? ' border-[0.5vh] border-solid ' : ' border-[0.4vh] border-dotted')
        + (actionablePlayer
            ? (yourActionablePlayer ? ' border-orange-500 shadow-actionSm hover:shadow-actionLg ' : ' border-teal-500 shadow-actionSmOp ')
            : (character ? configNetrunners.characters[character].classAvatarBorder : ' border-lime-600 ')
        )
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

            <div className={' flex items-center justify-center bg-bottom bg-no-repeat bg-cover w-full h-full rounded-full ' + (glitch ? ' animate-glitch ' : ' ')}
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

            {character && <BatteryChange playerName={playerName} />}

        </div>
    );
}
