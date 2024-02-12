import React from 'react';
import {createRoot} from 'react-dom/client';
import {NewGameSection} from "./components/NewGameSection.jsx";
import {NewGameLoginRedirect} from "./components/NewGameLoginRedirect.jsx";

const newGameSectionElement = document.querySelector('#js-single-new-game-section');

if (newGameSectionElement) {

    if (newGameSectionElement.dataset.auth) {

        const props = {
            'gameDefinition': Object.assign({}, JSON.parse(newGameSectionElement.dataset.gamedefinition)),
            'currentPlayerName': newGameSectionElement.dataset.currentplayername,
        }

        if (newGameSectionElement.dataset.currentgame) {
            props.currentGame = Object.assign({}, JSON.parse(newGameSectionElement.dataset.currentgame));
        }

        createRoot(newGameSectionElement).render(<NewGameSection {...props} />);

    } else {
        createRoot(newGameSectionElement).render(<NewGameLoginRedirect />);

    }
}
