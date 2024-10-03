import React from 'react';
import GamePlayController from "../../game-core/game-play/GamePlayController.jsx";
import {unstable_batchedUpdates} from "react-dom";
import {useGamePlayStore} from "../../game-core/game-play/useGamePlayStore.jsx";
import {Menu} from "../../../template/play/components/Menu.jsx";
import {FlashMessageGamePlay} from "../../game-core/game-play/FlashMessageGamePlay.jsx";
import {useNetrunnersStore} from "./useNetrunnersStore.jsx";
import {PlayersList} from "./elements/players/PlayersList.jsx";
import {CharactersGrid} from "./elements/players/CharactersGrid.jsx";

const getPlayersNames = (situation) => Object.getOwnPropertyNames(situation.players);

const setupSituation = (situation) => unstable_batchedUpdates(() => useNetrunnersStore.getState().setSituation(situation));

const setupMoveEvent = (e) => {

    const previousSituation = useNetrunnersStore.getState().situation;
    const hasSwitchedPlayerToActive = () => (
        e.situation.activePlayer === window.MyDramGames.player.name
        && previousSituation.activePlayer !== e.situation.activePlayer
    );

    unstable_batchedUpdates(() => {
        useNetrunnersStore.getState().setSituation(e.situation);

        if (e.situation.isFinished) {

            useGamePlayStore.getState().setFinished();
            useGamePlayStore.getState().setMessage(e.situation.result.message, false, 10);

        } else {

            useGamePlayStore.getState().setActivePlayer(e.situation.activePlayer);
            if (hasSwitchedPlayerToActive()) {
                useGamePlayStore.getState().setMessage('Your turn', false, 0.5);
            }
        }
    });
}

const controller = new GamePlayController(getPlayersNames, setupSituation, setupMoveEvent);


// Remove after testing
console.log(useNetrunnersStore.getState().situation);



controller.getRoot().render(
    <div className="relative w-full h-full">

        <div className="fixed top-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800 z-10">
            <Menu gameInvite={controller.getGameInvite()}/>
        </div>

        {/*--- Board ---*/}
        {/*Scrollable*/}
        {/*<div className="relative mt-[10vh] sm:mt-[12vh] pt-[2vh] w-full">*/}
        {/*Fixed*/}
        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-[80vh] sm:h-[76vh] bg-gray-900">

            {/*Add more sections as needed*/}

            <CharactersGrid />

        </div>

        <div className="fixed -bottom-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800">
            {/* Consider 'console' section with player tips and info here */}
        </div>

        <div className="fixed bottom-[10vh] sm:top-[12vh] w-full sm:w-fit left-0 sm:left-[2vh]">
            <div className="flex justify-center sm:justify-start">
                <PlayersList />
            </div>
        </div>

        <FlashMessageGamePlay/>

    </div>
);
