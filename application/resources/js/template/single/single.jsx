import React from 'react';
import {createRoot} from 'react-dom/client';
import {NewGameSection} from "./components/NewGameSection.jsx";
import {NewGameLoginRedirect} from "./components/NewGameLoginRedirect.jsx";

const newGameSectionElement = document.querySelector('#js-single-new-game-section');

if (newGameSectionElement) {

    if (newGameSectionElement.dataset.auth) {

        const props = {
            'gameDefinition': Object.assign({}, JSON.parse(newGameSectionElement.dataset.gamedefinition)),
            'storeUrl': newGameSectionElement.dataset.storeurl,
            'joinUrlBase': newGameSectionElement.dataset.joinurlbase,
        }
        createRoot(newGameSectionElement).render(<NewGameSection {...props} />);

    } else {

        const props = {
            'loginUrl': newGameSectionElement.dataset.loginurl,
        }
        createRoot(newGameSectionElement).render(<NewGameLoginRedirect {...props} />);

    }
}
