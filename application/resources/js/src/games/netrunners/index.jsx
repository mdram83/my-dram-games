import React from 'react';
import GamePlayController from "../../game-core/game-play/GamePlayController.jsx";
import {unstable_batchedUpdates} from "react-dom";
import {useGamePlayStore} from "../../game-core/game-play/useGamePlayStore.jsx";
import {Menu} from "../../../template/play/components/Menu.jsx";
import {FlashMessageGamePlay} from "../../game-core/game-play/FlashMessageGamePlay.jsx";
import {useNetrunnersStore} from "./useNetrunnersStore.jsx";
import {PlayersList} from "./elements/players/PlayersList.jsx";

// const playerName = window.MyDramGames.player.name;

const getPlayersNames = (situation) => Object.getOwnPropertyNames(situation.players);

const setupSituation = (situation) => unstable_batchedUpdates(() => useNetrunnersStore.getState().setSituation(situation));

const setupMoveEvent = (e) => {

    const previousSituation = useNetrunnersStore().getState().situation;
    const hasSwitchedPlayerToActive = () => (
        e.situation.activePlayer === window.MyDramGames.player.name
        && previousSituation.activePlayer !== e.situation.activePlayer
    );

    unstable_batchedUpdates(() => {
        useNetrunnersStore().getState().setSituation(e.situation);

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

console.log(useNetrunnersStore.getState().situation); // TODO remove after testing





controller.getRoot().render(
    <div className="relative w-full h-full">

        <div className="fixed top-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800 z-10">
            <Menu gameInvite={controller.getGameInvite()}/>
        </div>

        {/*--- Board ---*/}
        {/*Scrollable*/}
        {/*<div className="relative mt-[10vh] sm:mt-[12vh] pt-[2vh] w-full">*/}
        {/*Fixed*/}
        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-[80vh] sm:h-[76vh] bg-bottom bg-no-repeat bg-cover"
             // style={{ backgroundImage: `url(${window.MyDramGames["asset-url"].img + '/games/thousand/pexels-marta-wave-5875935-medium.jpg'})` }}
        >

            {/*In thousand this was central game section...*/}
            {/*<div className="grid grid-cols-3 gap-1 content-stretch h-full">*/}
            {/*    */}
            {/*</div>*/}

        </div>

        <div className="fixed -bottom-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800">
            {/*in thousand this was player section*/}
        </div>

        <div className="fixed bottom-[10vh] sm:top-[12vh] w-full z-10">
            <div className="flex justify-center sm:justify-start border border-solid border-red-600">
                <PlayersList />
            </div>
        </div>

        {/*in thousand below I had some fixed positioned elemenets*/}
        {/*<div className="fixed bottom-[10vh] w-full z-10">*/}
        {/*    <div className="flex justify-center">*/}
        {/*        */}
        {/*    </div>*/}
        {/*</div>*/}

        <FlashMessageGamePlay/>

    </div>
);
