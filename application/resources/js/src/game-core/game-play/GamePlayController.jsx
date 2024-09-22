import {useGamePlayStore} from "./useGamePlayStore.jsx";
import {unstable_batchedUpdates} from "react-dom";
import ConnectionsManager from "./ConnectionsManager.jsx";
import {createRoot} from "react-dom/client";

export default class GamePlayController {

    #rootElement = undefined;
    #gamePlayId = undefined;
    #gameInvite = {};
    #situation = {};

    constructor(getPlayersNames, setupSituation, setupMoveEvent) {

        this.#rootElement = document.querySelector('#game-play-root');
        this.#gamePlayId = this.#rootElement.dataset['game.id'];
        this.#gameInvite = Object.assign({}, JSON.parse(this.#rootElement.dataset['game.invite']));
        this.#situation = Object.assign({}, JSON.parse(this.#rootElement.dataset['game.situation']));

        this.#commonSetup(getPlayersNames(this.#situation));
        setupSituation(this.#situation);
        this.#playersChannelSetup();
        this.#playerChannelSetup(setupMoveEvent);
    }

    #commonSetup = (playerNames) => unstable_batchedUpdates(() => {
        useGamePlayStore.getState().setGamePlayId(this.#gamePlayId);
        useGamePlayStore.getState().setActivePlayer(this.#situation.activePlayer);
        playerNames.forEach((playerName) => useGamePlayStore.getState().setPlayer(playerName, false));
    });

    #playersChannelSetup = () =>
        Echo.join(`game-play-players.${this.#gamePlayId}`)
            .here((users) => {

                unstable_batchedUpdates(() => {

                    users.forEach((user) => useGamePlayStore.getState().setPlayer(user.name, true));

                    for (const [playerName, status] of Object.entries(useGamePlayStore.getState().players)) {
                        if (!status) {
                            ConnectionsManager.disconnect(
                                {name: playerName},
                                this.#gamePlayId,
                                this.#gameInvite.options.forfeitAfter
                            );
                        }
                    }
                });

                setTimeout(() => ConnectionsManager.confirmConnection(this.#gamePlayId), 2000);
            })
            .joining((user) => {

                unstable_batchedUpdates(() => {
                    useGamePlayStore.getState().setPlayer(user.name, true);
                    useGamePlayStore.getState().setMessage(user.name + ' connected.', false, 1);
                });

                ConnectionsManager.connectBack(user);
            })
            .leaving((user) => {

                unstable_batchedUpdates(() => {
                    useGamePlayStore.getState().setPlayer(user.name, false);
                    useGamePlayStore.getState().setMessage(user.name + ' disconnected.', true, 2);
                });

                ConnectionsManager.disconnect(user, this.#gamePlayId, this.#gameInvite.options.forfeitAfter);
            })
            .error(() => {});

    #playerChannelSetup = (setupMoveEvent) =>
        Echo.private(`game-play-player.${this.#gamePlayId}.${window.MyDramGames.player.id}`)
            .listen('GamePlay\\GamePlayMovedEvent', (e) =>
                setupMoveEvent(e)
            )
            .listen('GamePlay\\GamePlayDisconnectedEvent', () =>
                ConnectionsManager.confirmConnection(this.#gamePlayId)
            )
            .error((error) => {
                unstable_batchedUpdates(() => {
                    useGamePlayStore.getState().setMessage(error.status === 403 ? 'Authentication error' : 'Unexpected error', true);
                });
            });


    getRoot = () => createRoot(this.#rootElement);

    getGameInvite = () => this.#gameInvite;

    getSituation = () => this.#situation;
}
