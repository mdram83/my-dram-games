import React from 'react';
import {createRoot} from 'react-dom/client';
import {NewGameSection} from "./components/NewGameSection.jsx";
import {NewGameLoginRedirect} from "./components/NewGameLoginRedirect.jsx";

const newGameSectionElement = document.querySelector('#js-single-new-game-section');

if (newGameSectionElement) {

    if (window.MyDramGames.user.auth) {

        const props = {
            'gameDefinition': Object.assign({}, JSON.parse(newGameSectionElement.dataset['game.definition'])),
            'currentGame': newGameSectionElement.dataset['current.game']
                ? Object.assign({}, JSON.parse(newGameSectionElement.dataset['current.game']))
                : undefined,
        }

        createRoot(newGameSectionElement).render(<NewGameSection {...props} />);

    } else {
        createRoot(newGameSectionElement).render(<NewGameLoginRedirect />);

    }
}
