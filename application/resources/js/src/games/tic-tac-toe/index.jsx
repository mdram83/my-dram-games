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
            <BoardTicTacToe board={situation.board} />
        </div>

        {/*--- Status Bar ---*/}
        <div className="fixed bottom-0 w-full h-[16vh] sm:h-[12vh] px-[2%] py-[2vh] bg-gray-800">
            <StatusBarTicTacToe activePlayer={situation.activePlayer} characters={situation.characters} />
        </div>

    </div>
);

