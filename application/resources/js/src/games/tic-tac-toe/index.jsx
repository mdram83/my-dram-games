import React from 'react';
import {createRoot} from 'react-dom/client';
import {Menu} from "../../../template/play/components/Menu.jsx";
import {StatusBarTicTacToe} from "./elements/StatusBarTicTacToe.jsx";
import {BoardTicTacToe} from "./elements/BoardTicTacToe.jsx";
import {useTicTacToeStore} from "./elements/useTicTacToeStore.jsx";
import {unstable_batchedUpdates} from "react-dom";
import {FlashMessageTicTacToe} from "./elements/FlashMessageTicTacToe.jsx";
import {usePlayersStatusStore} from "../../../template/play/components/usePlayersStatusStore.jsx";
import {disconnect} from "../../game-core/game-play/disconnect.jsx";
import {connectBack} from "../../game-core/game-play/connectBack.jsx";
import {confirmConnection} from "../../game-core/game-play/confirmConnection.jsx";

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

Echo.join(`game-play-players.${gamePlayId}`)
    .here((users) => {
        unstable_batchedUpdates(() => {
            users.forEach((user) => usePlayersStatusStore.getState().setPlayer(user.name, true));
            for (const [playerName, status] of Object.entries(usePlayersStatusStore.getState().players)) {
                if (!status) {
                    disconnect({name: playerName}, gamePlayId, gameInvite.options.forfeitAfter);
                }
            }
        });
        setTimeout(() => confirmConnection(gamePlayId), 2000);
    })
    .joining((user) => {
        unstable_batchedUpdates(() => {
            usePlayersStatusStore.getState().setPlayer(user.name, true);
            useTicTacToeStore.getState().setMessage(user.name + ' connected.', false, 1);
        });
        connectBack(user);
    })
    .leaving((user) => {
        unstable_batchedUpdates(() => {
            usePlayersStatusStore.getState().setPlayer(user.name, false);
            useTicTacToeStore.getState().setMessage(user.name + ' disconnected.', true, 2);
        });
        disconnect(user, gamePlayId, gameInvite.options.forfeitAfter);
    })
    .error(() => {});

Echo.private(`game-play-player.${gamePlayId}.${window.MyDramGames.player.id}`)
    .listen('GameCore\\GamePlay\\GamePlayMovedEvent', (e) => unstable_batchedUpdates(() => {

        useTicTacToeStore.getState().setBoard(e.situation.board);

        if (e.situation.isFinished) {

            useTicTacToeStore.getState().setFinished();
            useTicTacToeStore.getState().setWinningFields(e.situation.result.details.winningFields);
            useTicTacToeStore.getState().setMessage(e.situation.result.message, false, 10);

        } else {

            useTicTacToeStore.getState().setActivePlayer(e.situation.activePlayer);

            if (e.situation.activePlayer === window.MyDramGames.player.name) {
                useTicTacToeStore.getState().setMessage('Your turn', false, 0.5);
            }
        }

    }))
    .listen('GameCore\\GamePlay\\GamePlayDisconnectedEvent', () => confirmConnection(gamePlayId))
    .error((error) => unstable_batchedUpdates(() => {
        useTicTacToeStore.getState().setMessage(
            error.status === 403 ? 'Authentication error' : 'Unexpected error',
            true
        );
    }));


createRoot(rootElement).render(

    <div className="relative w-full h-full">

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

        <div className="fixed bottom-0 w-full h-[16vh] sm:h-[12vh] px-[2%] py-[2vh] bg-gray-800">
            <StatusBarTicTacToe  characters={situation.characters} />
        </div>

        <FlashMessageTicTacToe />

    </div>
);

