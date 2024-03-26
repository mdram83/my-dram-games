import React from 'react';
import {createRoot} from 'react-dom/client';
import {Menu} from "../../../template/play/components/Menu.jsx";
import {StatusBarTicTacToe} from "./StatusBarTicTacToe.jsx";
import {BoardTicTacToe} from "./BoardTicTacToe";
import {useTicTacToeStore} from "./useTicTacToeStore.jsx";
import {unstable_batchedUpdates} from "react-dom";
import {ErrorMessageTicTacToe} from "./ErrorMessageTicTacToe";
import {usePlayersStatusStore} from "../../../template/play/components/usePlayersStatusStore.jsx";

const rootElement = document.querySelector('#game-play-root');

const gamePlayId = rootElement.dataset['game.id'];
const gameInvite = Object.assign({}, JSON.parse(rootElement.dataset['game.invite']));
const situation = Object.assign({}, JSON.parse(rootElement.dataset['game.situation']));

unstable_batchedUpdates(() => {
    useTicTacToeStore.getState().setGamePlayId(gamePlayId);
    useTicTacToeStore.getState().setActivePlayer(situation.activePlayer);
    useTicTacToeStore.getState().setBoard(situation.board);
    situation.players.forEach((playerName) => usePlayersStatusStore.getState().setPlayer(playerName, false));
});

// TODO add presence channel to hear for player connection status

Echo.private(`game-play-player.${gamePlayId}.${window.MyDramGames.player.id}`)
    .listen('GameCore\\GamePlay\\GamePlayMovedEvent', (e) => {
        unstable_batchedUpdates(() => {
            useTicTacToeStore.getState().setActivePlayer(e.situation.activePlayer);
            useTicTacToeStore.getState().setBoard(e.situation.board);
        })
    })
    .error((error) => {
        unstable_batchedUpdates(() => {
            useTicTacToeStore.getState().setErrorMessage(error.status === 403 ? 'Authentication error' : 'Unexpected error');
        })
    });


createRoot(rootElement).render(

    <div className="relative w-full h-full">

        {/*--- Menu ---*/}
        <div className="fixed top-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800 z-10">
            <Menu gameInvite={gameInvite} />
        </div>


        {/*--- Board ---*/}

        {/*Scrollable*/}
        {/*<div className="relative mt-[10vh] sm:mt-[12vh] pt-[2vh] w-full">*/}
        {/*Fixed*/}
        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-[74vh]">
            <BoardTicTacToe  />
        </div>

        {/*--- Status Bar ---*/}
        <div className="fixed bottom-0 w-full h-[16vh] sm:h-[12vh] px-[2%] py-[2vh] bg-gray-800">
            <StatusBarTicTacToe  characters={situation.characters} />
        </div>

        {/*--- Error Message ---*/}
        <ErrorMessageTicTacToe />

    </div>
);

