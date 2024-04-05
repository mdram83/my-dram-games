import axios from "axios";
import {unstable_batchedUpdates} from "react-dom";
import {useDisconnectionStore} from "./useDisconnectionStore.jsx";
import {disconnectForfeit} from "./disconnectForfeit.jsx";

export const disconnect = (user, gamePlayId, forfeitAfterTimeout = 0) => {

    const processingDelaySeconds = 2;

    unstable_batchedUpdates(() => useDisconnectionStore.getState().addDisconnection(
        user.name,
        () => forfeitAfterTimeout > 0 ? disconnectForfeit(user, gamePlayId) : {},
        forfeitAfterTimeout + processingDelaySeconds
    ));

    axios
        .post(window.MyDramGames.routes['ajax.gameplay.disconnect'](gamePlayId), {disconnected: user.name})
        .then(() => {})
        .catch(() => {});
}
