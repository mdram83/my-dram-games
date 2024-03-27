import React from 'react';
import {createRoot} from 'react-dom/client';
import {GameInviteControls} from "../../../src/game-core/game-invite/GameInviteControls.jsx";

const rootElement = document.querySelector('#single-game-invite-controls-root');

if (rootElement) {
    const props = {
        'gameBox': Object.assign({}, JSON.parse(rootElement.dataset['game.box'])),
        'loadedGameInvite': rootElement.dataset['game.invite']
            ? Object.assign({}, JSON.parse(rootElement.dataset['game.invite']))
            : undefined,
        'gamePlayId': rootElement.dataset['game.playid'] ?? undefined,
    }

    createRoot(rootElement).render(<GameInviteControls {...props} />);
}
