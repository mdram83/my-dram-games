import React from 'react';
import {createRoot} from 'react-dom/client';
import {NewGameSection} from "./components/NewGameSection.jsx";

const newGameSectionElement = document.querySelector('#js-single-new-game-section');

if (newGameSectionElement) {
    const props = {
        'gameBox': Object.assign({}, JSON.parse(newGameSectionElement.dataset['game.box'])),
        'loadedGameInvite': newGameSectionElement.dataset['game.invite']
            ? Object.assign({}, JSON.parse(newGameSectionElement.dataset['game.invite']))
            : undefined,
    }

    createRoot(newGameSectionElement).render(<NewGameSection {...props} />);
}
