import React from 'react';
import {createRoot} from 'react-dom/client';
import {NewGameSection} from "./components/NewGameSection.jsx";

const newGameSectionElement = document.querySelector('#js-single-new-game-section');

if (newGameSectionElement) {

    const props = {
        'gameDefinition': Object.assign({}, JSON.parse(newGameSectionElement.dataset.gamedefinition)),
        'storeUrl': newGameSectionElement.dataset.storeurl,
        'joinUrlBase': newGameSectionElement.dataset.joinurlbase,
    }

    createRoot(newGameSectionElement).render(<NewGameSection {...props} />);
}





