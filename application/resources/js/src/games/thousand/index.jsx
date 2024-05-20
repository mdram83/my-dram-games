import React from 'react';
import GamePlayController from "../../game-core/game-play/GamePlayController.jsx";
import {unstable_batchedUpdates} from "react-dom";
import {useGamePlayStore} from "../../game-core/game-play/useGamePlayStore.jsx";
import {Menu} from "../../../template/play/components/Menu.jsx";
import {FlashMessageGamePlay} from "../../game-core/game-play/FlashMessageGamePlay.jsx";
import {useThousandStore} from "./useThousandStore.jsx";

const getPlayersNames = (situation) => Object.getOwnPropertyNames(situation.orderedPlayers);

const setupSituation = (situation) => unstable_batchedUpdates(
    () => useThousandStore.getState().setSituation(situation)
);

const setupMoveEvent = (e) =>
    unstable_batchedUpdates(() => {
        useThousandStore.getState().setSituation(e.situation);

        if (e.situation.isFinished) {

            useGamePlayStore.getState().setFinished();
            useGamePlayStore.getState().setMessage(e.situation.result.message, false, 10);

            // useTicTacToeStore.getState().setWinningFields(e.situation.result.details.winningFields); // TODO adjust for thousand

        } else {

            useGamePlayStore.getState().setActivePlayer(e.situation.activePlayer);

            if (e.situation.activePlayer === window.MyDramGames.player.name) {
                useGamePlayStore.getState().setMessage('Your turn', false, 0.5);
            }
        }
    });

const controller = new GamePlayController(getPlayersNames, setupSituation, setupMoveEvent);

// TODO remove after testing
console.log(useThousandStore.getState().situation);
console.log(useGamePlayStore.getState());

controller.getRoot().render(

    <div className="relative w-full h-full">

        <div className="fixed top-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800 z-10">
            <Menu gameInvite={controller.getGameInvite()} />
        </div>

        {/*--- Board ---*/}
        {/*Scrollable*/}
        {/*<div className="relative mt-[10vh] sm:mt-[12vh] pt-[2vh] w-full">*/}
        {/*Fixed*/}
        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-[74vh]">
            Board goes here
        </div>

        <div className="fixed bottom-0 w-full h-[16vh] sm:h-[12vh] px-[2%] py-[2vh] bg-gray-800">
            Status Bar goes here
        </div>

        <FlashMessageGamePlay />

    </div>
);

