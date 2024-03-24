import React from 'react';
import {createRoot} from 'react-dom/client';
import {Menu} from "../../../template/play/components/Menu.jsx";
import {StatusBarTicTacToe} from "./StatusBarTicTacToe.jsx";

const rootElement = document.querySelector('#game-play-root');

const gamePlayId = rootElement.dataset['game.id'];
const gameInvite = Object.assign({}, JSON.parse(rootElement.dataset['game.invite']));
const situation = Object.assign({}, JSON.parse(rootElement.dataset['game.situation']));

// console.log(gamePlayId, gameInvite, situation);
console.log(situation);

createRoot(rootElement).render(

    <div className="relative w-full h-full">

        {/*--- Menu ---*/}
        <div className="fixed top-0 w-full h-16 sm:h-24 bg-gray-800 border border-solid border-blue-300">
            <Menu gameInvite={gameInvite} />
        </div>

        {/*--- Board ---*/}
        <div className="fixed top-0 mt-16 sm:mt-20 mb-36 sm:top-24 w-full h-1/2 border-4 border-solid border-red-500">
            {/*relative mt-16 sm:mt-24 p-4*/}
            {/*TODO Board to play goes here*/}
            <p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p>
            <p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p><p>Content</p>
        </div>

        {/*--- Status Bar ---*/}
        <div className="fixed bottom-0 w-full h-max px-4 sm:px-1 py-2 sm:py-4 bg-gray-800 border-4 border-solid border-yellow-500">
            <StatusBarTicTacToe activePlayer={situation.activePlayer} characters={situation.characters} />
        </div>

    </div>
);

