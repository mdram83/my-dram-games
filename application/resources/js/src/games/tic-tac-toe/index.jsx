import React from 'react';
import {createRoot} from 'react-dom/client';
import {Menu} from "../../../template/play/components/Menu.jsx";

const rootElement = document.querySelector('#game-play-root');

const gamePlayId = rootElement.dataset['game.id'];
const gameInvite = Object.assign({}, JSON.parse(rootElement.dataset['game.invite']));
const situation = Object.assign({}, JSON.parse(rootElement.dataset['game.situation']));

// console.log(gamePlayId, gameInvite, situation);

createRoot(rootElement).render(
    <Menu gameInvite={gameInvite} />

    // TODO Board to play goes here
    // TODO Footer with players go here
);

