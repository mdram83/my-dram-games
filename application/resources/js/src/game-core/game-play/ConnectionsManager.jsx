import axios from "axios";
import {unstable_batchedUpdates} from "react-dom";
import {useGamePlayStore} from "./useGamePlayStore.jsx";

export default class ConnectionsManager {

    static processingDelaySeconds = 2;

    static disconnect(user, gamePlayId, forfeitAfterTimeout = 0) {

        unstable_batchedUpdates(() => useGamePlayStore.getState().addDisconnection(
            user.name,
            () => forfeitAfterTimeout > 0 ? ConnectionsManager.disconnectForfeit(user, gamePlayId) : {},
            forfeitAfterTimeout + ConnectionsManager.processingDelaySeconds
        ));

        axios
            .post(window.MyDramGames.routes['ajax.gameplay.disconnect'](gamePlayId), {disconnected: user.name})
            .then(() => {})
            .catch(() => {});
    }

    static disconnectForfeit(user, gamePlayId) {
        axios
            .post(window.MyDramGames.routes['ajax.gameplay.disconnect-forfeit'](gamePlayId), {disconnected: user.name})
            .then(() => {})
            .catch(() => {});
    }

    static confirmConnection(gamePlayId) {
        axios
            .get(window.MyDramGames.routes['ajax.gameplay.connect'](gamePlayId))
            .then(() => {})
            .catch(() => {});
    }

    static connectBack(user) {
        unstable_batchedUpdates(() => useGamePlayStore.getState().removeDisconnection(user.name));
    }
}
