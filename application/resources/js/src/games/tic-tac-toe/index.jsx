import React from 'react';
import {createRoot} from 'react-dom/client';
import {Menu} from "../../../template/play/components/Menu.jsx";
import {StatusBarTicTacToe} from "./StatusBarTicTacToe.jsx";
import {BoardTicTacToe} from "./BoardTicTacToe";

const rootElement = document.querySelector('#game-play-root');

const gamePlayId = rootElement.dataset['game.id'];
const gameInvite = Object.assign({}, JSON.parse(rootElement.dataset['game.invite']));
const situation = Object.assign({}, JSON.parse(rootElement.dataset['game.situation']));

// console.log(gamePlayId, gameInvite, situation);

// TODO adjust styling (cover scenario when someone flip the mobile...

createRoot(rootElement).render(

    <div className="relative w-full h-full">

        {/*--- Menu ---*/}
        <div className="fixed top-0 w-full h-16 sm:h-24 bg-gray-800 z-10">
            <Menu gameInvite={gameInvite} />
        </div>


        {/*--- Board ---*/}

        {/*Alternative div positioning options*/}

        {/*Scrollable*/}
        {/*<div className="relative mt-16 sm:mt-24 w-full">*/}

        {/*Fixed*/}
        <div className="fixed mt-16 sm:mt-24 w-full h-[74%] sm:h-[77%]">
            <BoardTicTacToe board={situation.board} />
        </div>

        {/*--- Status Bar ---*/}
        <div className="fixed bottom-0 w-full h-max px-4 sm:px-1 py-2 sm:py-4 bg-gray-800">
            <StatusBarTicTacToe activePlayer={situation.activePlayer} characters={situation.characters} />
        </div>

    </div>
);

